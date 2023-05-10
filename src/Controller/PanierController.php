<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LignepanierRepository;
use App\Entity\Lignepanier;
use App\Entity\Panier;
use App\Entity\User;
use App\Entity\Commande;
use App\Repository\PanierRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\CommandeFormType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Attachment;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    //Fonction qui retourne les produits du panier d'un user ainsi que le montant total de son panier
    #[Route('/affichepanier', name: 'app_Affichepanier')]
    public function ProduitsParPanier(PanierRepository $panierRep,UserRepository $ur,LignepanierRepository $rep):Response
    {
        $user=$this->getUser();
        $panier = $panierRep->findOneBy(['user' => $user]);
        $idPanier=$user->getIdUser();
        $produits = $rep->findBy(['panier' => $idPanier]); 
        $total = 0;      
        $nbp = 0;
        foreach ($produits as $lignePanier) {
            $quantite = $lignePanier->getQuantite();
            $prixUnitaire = $lignePanier->getPrix();
            $total += $quantite * $prixUnitaire;
            $nbp += 1;
        }

       
        
    return $this->render('panier/afficherpanier.html.twig', [
        'produits' => $produits,
        'total' => $total,
        'panier' => $panier,
        'user'=>$user,
    ]);
    }


    
    //Fonction qui retourne les produits du panier d'un user sous format json 
    #[Route('/affichepanierJSON', name: 'app_AffichepanierJSON')]
    public function ProduitsParPanierJson(Request $request,PanierRepository $panierRep,UserRepository $ur,LignepanierRepository $rep, NormalizerInterface $Normalizer):Response
    {
        $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
        $panier = $panierRep->findOneBy(['user' => $user]);
        $idPanier=$user->getIdUser();
        $produits = $rep->findBy(['panier' => $idPanier]); 

       
        $jsonContent = $Normalizer->normalize($produits,'json',['groups'=>'lignepanier']);
        return new Response(json_encode($jsonContent));
    }


     //Fonction qui retourne le nombre de produits dans le panier d'un user
     #[Route('/nbprod', name: 'app_nbprod')]
     public function nbprod(PanierRepository $panierRep,UserRepository $ur,LignepanierRepository $rep):Response
     {
        $user=$this->getUser();
         $panier = $panierRep->findOneBy(['user' => $user]);
         $idPanier=$panier->getId();
         $produits = $rep->findBy(['panier' => $idPanier]);    
         $nbp = 0;
         foreach ($produits as $l) {
             $nbp += 1;
         }

       // Retourner une réponse JSON avec le nombre de produits
       return $this->json(['nbp' => $nbp]);
     }


    //Fonction qui met a jours la qantité +1 d'un produit dans un panier
    #[Route('/qtPlusUn/{id}', name: 'plus1')]
    public function updateLignePanierQuantitePlusUn(int $id, ManagerRegistry $doctrine,LignepanierRepository $rep)
{
    $entityManager = $doctrine->getManager();
    $lignePanier = $rep->find($id);
    $lignePanier->setQuantite($lignePanier->getQuantite() + 1);

    $entityManager->persist($lignePanier);
    $entityManager->flush();

    // Rediriger vers la page d'affichage du panier
    return $this->redirectToRoute("app_Affichepanier");
}

    #[Route('/qtPlusUnJSON/{id}', name: 'plus1JSON')]
    public function updateLignePanierQuantitePlusUnJSON(int $id, ManagerRegistry $doctrine,LignepanierRepository $rep,NormalizerInterface $Normalizer)
{
    $entityManager = $doctrine->getManager();
    $lignePanier = $rep->find($id);
    $lignePanier->setQuantite($lignePanier->getQuantite() + 1);

    $entityManager->persist($lignePanier);
    $entityManager->flush();

    $jsonContent = $Normalizer->normalize($lignePanier,'json',['groups'=>'Lignepanier']);
    return new Response("Quantité mise à jour avec succès".json_encode($jsonContent));
 }

    
    //Fonction qui met a jours la qantité -1 d'un produit dans un panier
    #[Route('/qtMoinsUn/{id}', name: 'moins1')]
    public function updateLignePanierQuantiteMoinsUn(int $id, ManagerRegistry $doctrine,LignepanierRepository $rep)
{
    $entityManager = $doctrine->getManager();
    $lignePanier = $rep->find($id);
    $lignePanier->setQuantite($lignePanier->getQuantite() - 1);

    $entityManager->persist($lignePanier);
    $entityManager->flush();

   // Rediriger vers la page d'affichage du panier
   return $this->redirectToRoute("app_Affichepanier");

       // Récupérer la nouvelle quantité
       $quantite = $lignePanier->getQuantite();
       $prix = $lignePanier->getPrix();
}

    #[Route('/qtMoinsUnJSON/{id}', name: 'moins1JSON')]
    public function updateLignePanierQuantiteMoinsUnJSON(int $id, ManagerRegistry $doctrine,LignepanierRepository $rep,NormalizerInterface $Normalizer)
{
    $entityManager = $doctrine->getManager();
    $lignePanier = $rep->find($id);
    $lignePanier->setQuantite($lignePanier->getQuantite() - 1);

    $entityManager->persist($lignePanier);
    $entityManager->flush();
    $jsonContent = $Normalizer->normalize($lignePanier,'json',['groups'=>'Lignepanier']);
    return new Response("Quantité mise à jour avec succès".json_encode($jsonContent));


}


   //Fonction qui supprime un produit du panier 
   #[Route('SupprimerProduit/{id}', name: 'supprimer_ligne_panier')]
   public function deleteLignePanier(int $id,ManagerRegistry $doctrine,LignepanierRepository $rep): Response
{
    $entityManager = $doctrine->getManager();
    $lignePanier = $rep->find($id);
       // Récupérer l'ID du panier
   $idPanier = $lignePanier->getPanier()->getId();
    //Supprimer la ligne panier
    $entityManager->remove($lignePanier);
    $entityManager->flush();
   // Rediriger vers la page d'affichage du panier en passant l'ID du panier
   return $this->redirectToRoute("app_Affichepanier", ['idPanier' => $idPanier]);
}

   //Fonction qui supprime un produit du panier 
   #[Route('SupprimerProduitJSON/{id}', name: 'supprimer_ligne_panierJSON')]
   public function deleteLignePanierJSON(int $id,ManagerRegistry $doctrine,LignepanierRepository $rep,NormalizerInterface $Normalizer): Response
{
    $entityManager = $doctrine->getManager();
    $lignePanier = $rep->find($id);
    //Supprimer la ligne panier
    $entityManager->remove($lignePanier);
    $entityManager->flush();
    $jsonContent = $Normalizer->normalize($lignePanier,'json',['groups'=>'Lignepanier']);
    return new Response("LignePAnier Supprimé avec succès".json_encode($jsonContent));
    
}

  //Fonction qui vide le panier d'un user 
  #[Route('/ViderPanier/{id}', name: 'vider_panier')]
  public function ViderPanier(int $id,ManagerRegistry $doctrine,LignepanierRepository $rep,PanierRepository $panierRep): Response
{   

    $entityManager = $doctrine->getManager();
    $lignesPanier = $rep->findBy(['panier' => $id]);
        

    foreach ($lignesPanier as $lignePanier) {
        $entityManager->remove($lignePanier);
        // Récupérer l'ID du panier
   $idPanier = $lignePanier->getPanier()->getId();
    }

    $entityManager->flush();
   // Rediriger vers la page d'affichage du panier en passant l'ID du panier
   return $this->redirectToRoute("app_Affichepanier", ['idPanier' => $idPanier]);
}

//Fonction qui vide le panier d'un user 
#[Route('/ViderPanierJSON', name: 'vider_panierJSON')]
public function ViderPanierJSON(ManagerRegistry $doctrine,LignepanierRepository $rep,PanierRepository $panierRep,UserRepository $ur,NormalizerInterface $Normalizer): Response
{

  $user=$this->getUser();
  $panier = $panierRep->findOneBy(['user' => $user]);
  $idPanier=$panier->getId();
  $lignesPanier = $rep->findBy(['panier' => $$idPanier]);
  $entityManager = $doctrine->getManager();

      

  foreach ($lignesPanier as $lignePanier) {
      $entityManager->remove($lignePanier);
  }

  $entityManager->flush();
  $jsonContent = $Normalizer->normalize($lignePanier,'json',['groups'=>'Lignepanier']);
  return new Response("Panier Vidé avec succès".json_encode($jsonContent));
}


#[Route('/imprimerfacture/{idCommande}', name: 'app_facture')]
public function imprimerFacture(int $idCommande,LignepanierRepository $lr,PanierRepository $pr,UserRepository $ur,CommandeRepository $cr,TransportInterface $mailer ): Response
{
    $userr=$this->getUser();
        $user = $ur->find($userr);
    $panier = $pr->findOneBy(['user' => $user]);
    $id=$panier->getId();
    $produits_par_panier=$lr->findBy(['panier' => $panier]);
    $commande = $cr->find($idCommande);
    $publicPath = $this->getParameter('kernel.project_dir') . '/public';
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    //$pdfOptions->set('isRemoteEnabled', true);
    $pdf = new Dompdf($pdfOptions);
    //$pdf->loadHtml($aData['html']);
    $pdf->set_option('isRemoteEnabled', TRUE);
    //Contenu du Pdf
    $html = '<html><body>';
    $html .= ' <div style="background-color: red; height: 50px; display: flex; justify-content: center; align-items: center;">
  <span style="color: white; font-size: 24px; text-align:center">Drift&Race</span>
</div>';
    $html .= '<h1>Facture</h1>';
    // $html .= '<img src="' . $publicPath . '/en_tete.png" />';
    //$html .= '<img src="public/images/en_tete.png" />';
    //$html .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($publicPath.'/en_tete.png')).'" />';
    $html .= '<p>Vendeur : Drift$Race</p>';
    $html .= '<p>_________________________________________________________________________</p>';
    $html .= '<p>Client : '.$user->getNom().' '.$user->getPrenom().'</p>';
    $html .= '<p>Commande n°'.$commande->getId().'</p>';
    $html .= '<p>Date de facturation : '.$commande->getDateCommande()->format('d/m/Y').'</p>';
    $html .= '<p>Adresse de livraison : '.$commande->getRue().' '.$commande->getVille().' '.$commande->getGouvernorat().', '.$commande->getCodePostal().'</p>';
    $html .= '<p>________________________________________________________________________</p>';
    $html .= '<h3>Détails de la commande</h3>';
    $html .= '<table border="1" cellspacing="0" style="width: 100%; text-align: center;">';
    $html .= '<tr style="background-color: black; color: white; font-weight: bold;"><th>Référence du Paroduit</th><th>Produit</th><th>Prix unitaire</th><th>Quantité</th><th>Sous Montant</th></tr>';
    foreach ($produits_par_panier as $p) {
        $html .= '<tr><td>'.$p->getId().'</td><td>'.$p->getProduit()->getNomProd().'</td><td>'.$p->getProduit()->getPrixProd().' DT</td><td>'.$p->getQuantite().'</td><td>'.$p->getProduit()->getPrixProd()*$p->getQuantite().' DT</td></tr>';
    }
    $html .= '<tr><td colspan="4">Montant Total à payer</td><td>'.$commande->getMontant().' DT</td></tr>';
    $html .= '</table>';
   // $html .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($publicPath.'/bas_page.png')).'" />';
    //$html .= '<img src="' . $publicPath . '/bas_page.png" />';
    //$html .= '<img src="public/images/bas_page.png" />';

    $html .= ' <div style="position:absolute; bottom:0; width:100%; height:30px; background-color:red;">
    <p style="color:white; font-weight:bold; margin:0 0 0 20px; line-height:30px;">Drift&Race</p>
  </div>';
    $html .= '</body></html>';
    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'portrait');
     // Render the PDF as a string
     $pdf->render();

     // Retourner le pdf
     $output = $pdf->output();
     //file_put_contents('downloads/facture.pdf', $output);
     // Rendre la template Twig en HTML
   $contenu = $this->renderView('mail.html.twig');

    // Créer l'email
    $email = (new Email())
    ->from('helamoalla91@gmail.com')
    ->to($user->getEmail())
    ->subject('Facture')
    ->html($contenu)
    ->attach($output, 'facture.pdf','downloads/facture.pdf');

   // Envoyer l'email
   $mailer->send($email);

    return $this->redirectToRoute("vider_panier", ['id' => $id]);

    //return $this->redirectToRoute("app_Affichepanier");
}



#[Route('/passercommande/{idPanier}/{total}', name: 'passer_commande')]
public function passerCommande(int $idPanier,FlashyNotifier $flashy, float $total,ManagerRegistry $doctrine,Request $request): Response
{
    // Récupérer le panier
    $entityManager = $doctrine->getManager();
    $panier = $entityManager->getRepository(Panier::class)->find($idPanier);
    
    $commande = new Commande();
    $form=$this->createForm(CommandeFormType::class,$commande);
    $form->handleRequest($request);
    $dateCommande = new \DateTime();


    if ($form->isSubmitted() && $form->isValid()) {
        $commande->setPanier($panier);
        $commande->setMontant($total);
        $commande->setEtatCommande("En cours de traitement");
        $commande->setDateCommande($dateCommande);
        $entityManager = $doctrine->getManager() ;
        $user=$this->getUser();
        if($total<$user->getQuota()){

$user->setQuota($user->getQuota()-$total);
        $entityManager->persist($commande);
        $entityManager->flush();
     
        //nrecuperi l commande li tsan3et jdida
        $idCommande = $commande->getId();
        //redirection lel route mtaa l facture w naatih l id commande li tsan3et jdida
        return $this->redirectToRoute("app_facture", ['idCommande' => $idCommande]);}
      
    }
    $this->addFlash('error1', 'solde insuffisant');
    $flashy->error('votre solde est insuffisant', 'http://your-awesome-link.com');
    return $this->renderForm("commande/passercommande.html.twig",
        array("f"=>$form));
}



#[Route('/imprimerfacturejson/{idCommande}', name: 'app_facturejson')]
public function imprimerFacturejson(Request $request,int $idCommande,LignepanierRepository $lr,PanierRepository $pr,UserRepository $ur,CommandeRepository $cr,TransportInterface $mailer ): Response
{
    $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
    $panier = $pr->findOneBy(['user' => $user]);
    $id=$panier->getId();
    $produits_par_panier=$lr->findBy(['panier' => $panier]);
    $commande = $cr->find($idCommande);
    $publicPath = $this->getParameter('kernel.project_dir') . '/public';
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    //$pdfOptions->set('isRemoteEnabled', true);
    $pdf = new Dompdf($pdfOptions);
    //$pdf->loadHtml($aData['html']);
    $pdf->set_option('isRemoteEnabled', TRUE);
    //Contenu du Pdf
    $html = '<html><body>';
    $html .= ' <div style="background-color: red; height: 50px; display: flex; justify-content: center; align-items: center;">
  <span style="color: white; font-size: 24px; text-align:center">Drift&Race</span>
</div>';
    $html .= '<h1>Facture</h1>';
    // $html .= '<img src="' . $publicPath . '/en_tete.png" />';
    //$html .= '<img src="public/images/en_tete.png" />';
    //$html .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($publicPath.'/en_tete.png')).'" />';
    $html .= '<p>Vendeur : Drift$Race</p>';
    $html .= '<p>_________________________________________________________________________</p>';
    $html .= '<p>Client : '.$user->getNom().' '.$user->getPrenom().'</p>';
    $html .= '<p>Commande n°'.$commande->getId().'</p>';
    $html .= '<p>Date de facturation : '.$commande->getDateCommande()->format('d/m/Y').'</p>';
    $html .= '<p>Adresse de livraison : '.$commande->getRue().' '.$commande->getVille().' '.$commande->getGouvernorat().', '.$commande->getCodePostal().'</p>';
    $html .= '<p>________________________________________________________________________</p>';
    $html .= '<h3>Détails de la commande</h3>';
    $html .= '<table border="1" cellspacing="0" style="width: 100%; text-align: center;">';
    $html .= '<tr style="background-color: black; color: white; font-weight: bold;"><th>Référence du Paroduit</th><th>Produit</th><th>Prix unitaire</th><th>Quantité</th><th>Sous Montant</th></tr>';
    foreach ($produits_par_panier as $p) {
        $html .= '<tr><td>'.$p->getId().'</td><td>'.$p->getProduit()->getNomProd().'</td><td>'.$p->getProduit()->getPrixProd().' DT</td><td>'.$p->getQuantite().'</td><td>'.$p->getProduit()->getPrixProd()*$p->getQuantite().' DT</td></tr>';
    }
    $html .= '<tr><td colspan="4">Montant Total à payer</td><td>'.$commande->getMontant().' DT</td></tr>';
    $html .= '</table>';
   // $html .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($publicPath.'/bas_page.png')).'" />';
    //$html .= '<img src="' . $publicPath . '/bas_page.png" />';
    //$html .= '<img src="public/images/bas_page.png" />';

    $html .= ' <div style="position:absolute; bottom:0; width:100%; height:30px; background-color:red;">
    <p style="color:white; font-weight:bold; margin:0 0 0 20px; line-height:30px;">Drift&Race</p>
  </div>';
    $html .= '</body></html>';
    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'portrait');
     // Render the PDF as a string
     $pdf->render();

     // Retourner le pdf
     $output = $pdf->output();
     //file_put_contents('downloads/facture.pdf', $output);
     // Rendre la template Twig en HTML
   $contenu = $this->renderView('mail.html.twig');

    // Créer l'email
    $email = (new Email())
    ->from('helamoalla91@gmail.com')
    ->to($user->getEmail())
    ->subject('Facture')
    ->html($contenu)
    ->attach($output, 'facture.pdf','downloads/facture.pdf');

   // Envoyer l'email
   $mailer->send($email);

    return $this->redirectToRoute("vider_panier", ['id' => $id]);

    //return $this->redirectToRoute("app_Affichepanier");
}




#[Route('/passercommandeJSON', name: 'passer_commandejson')]
public function passerCommandejson(TransportInterface $mailer,CommandeRepository $cr,LignepanierRepository $lr,PanierRepository $pr,ManagerRegistry $doctrine,Request $request,UserRepository $ur,LignepanierRepository $rep,PanierRepository $panierRep,NormalizerInterface $Normalizer): Response
{
    $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
    $panier = $panierRep->findOneBy(['user' => $user]);
    $idPanier=$panier->getId();
    $produits = $rep->findBy(['panier' => $idPanier]); 
    $total = 0;      
  
    foreach ($produits as $lignePanier) {
        $quantite = $lignePanier->getQuantite();
        $prixUnitaire = $lignePanier->getPrix();
        $total += $quantite * $prixUnitaire;
    }
    
    $commande = new Commande();
    $dateCommande = new \DateTime();

    $commande->setPanier($panier);
    $commande->setMontant($total);
    $commande->setEtatCommande("En cours de traitement");
    $commande->setGouvernorat($request->get('gouvernorat'));
    $commande->setVille($request->get('ville'));
    $commande->setRue($request->get('rue'));
    $commande->setCodePostal((int)$request->get('code_postal'));
    $commande->setDateCommande($dateCommande);
    
    $em =$doctrine->getManager() ;
     
        $em->persist($commande);
        $em->flush();
        
        $idCommande = $commande->getId();
        //redirection lel route mtaa l facture w naatih l id commande li tsan3et jdida

        $panier = $pr->findOneBy(['user' => $user]);
        $id=$panier->getId();
        $produits_par_panier=$lr->findBy(['panier' => $panier]);
        $commande = $cr->find($idCommande);
        $publicPath = $this->getParameter('kernel.project_dir') . '/public';
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        //$pdfOptions->set('isRemoteEnabled', true);
        $pdf = new Dompdf($pdfOptions);
        //$pdf->loadHtml($aData['html']);
        $pdf->set_option('isRemoteEnabled', TRUE);
        //Contenu du Pdf
        $html = '<html><body>';
        $html .= ' <div style="background-color: red; height: 50px; display: flex; justify-content: center; align-items: center;">
      <span style="color: white; font-size: 24px; text-align:center">Drift&Race</span>
    </div>';
        $html .= '<h1>Facture</h1>';
        // $html .= '<img src="' . $publicPath . '/en_tete.png" />';
        //$html .= '<img src="public/images/en_tete.png" />';
        //$html .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($publicPath.'/en_tete.png')).'" />';
        $html .= '<p>Vendeur : Drift$Race</p>';
        $html .= '<p>_________________________________________________________________________</p>';
        $html .= '<p>Client : '.$user->getNom().' '.$user->getPrenom().'</p>';
        $html .= '<p>Commande n°'.$commande->getId().'</p>';
        $html .= '<p>Date de facturation : '.$commande->getDateCommande()->format('d/m/Y').'</p>';
        $html .= '<p>Adresse de livraison : '.$commande->getRue().' '.$commande->getVille().' '.$commande->getGouvernorat().', '.$commande->getCodePostal().'</p>';
        $html .= '<p>________________________________________________________________________</p>';
        $html .= '<h3>Détails de la commande</h3>';
        $html .= '<table border="1" cellspacing="0" style="width: 100%; text-align: center;">';
        $html .= '<tr style="background-color: black; color: white; font-weight: bold;"><th>Référence du Paroduit</th><th>Produit</th><th>Prix unitaire</th><th>Quantité</th><th>Sous Montant</th></tr>';
        foreach ($produits_par_panier as $p) {
            $html .= '<tr><td>'.$p->getId().'</td><td>'.$p->getProduit()->getNomProd().'</td><td>'.$p->getProduit()->getPrixProd().' DT</td><td>'.$p->getQuantite().'</td><td>'.$p->getProduit()->getPrixProd()*$p->getQuantite().' DT</td></tr>';
        }
        $html .= '<tr><td colspan="4">Montant Total à payer</td><td>'.$commande->getMontant().' DT</td></tr>';
        $html .= '</table>';
       // $html .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($publicPath.'/bas_page.png')).'" />';
        //$html .= '<img src="' . $publicPath . '/bas_page.png" />';
        //$html .= '<img src="public/images/bas_page.png" />';
    
        $html .= ' <div style="position:absolute; bottom:0; width:100%; height:30px; background-color:red;">
        <p style="color:white; font-weight:bold; margin:0 0 0 20px; line-height:30px;">Drift&Race</p>
      </div>';
        $html .= '</body></html>';
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
         // Render the PDF as a string
         $pdf->render();
    
         // Retourner le pdf
         $output = $pdf->output();
         //file_put_contents('downloads/facture.pdf', $output);
         // Rendre la template Twig en HTML
       $contenu = $this->renderView('mail.html.twig');
    
        // Créer l'email
        $email = (new Email())
        ->from('helamoalla91@gmail.com')
        ->to($user->getEmail())
        ->subject('Facture')
        ->html($contenu)
        ->attach($output, 'facture.pdf','downloads/facture.pdf');
    
       // Envoyer l'email
       $mailer->send($email);


      //  return $this->redirectToRoute("app_facturejson", ['idCommande' => $idCommande]);
    
        $jsonContent = $Normalizer->normalize($commande,'json',['groups'=>'commande']);
        return new Response(json_encode($jsonContent));

}

}
