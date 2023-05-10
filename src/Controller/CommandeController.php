<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Commande;
use App\Entity\Panier;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/commandesAffiche/{idPanier}', name: 'afficher_commandes')]
public function afficherCommandesParPanier($idPanier)
{
    return $this->redirectToRoute("interface_affichage_commandes", ['idPanier' => $idPanier]);
}


#[Route('/commandes/{idPanier}', name: 'interface_affichage_commandes')]
public function afficherCommandes(int $idPanier): Response
{
    $commandes = $this->getDoctrine()->getRepository(Commande::class)->findBy(['panier' => $idPanier]);

    return $this->render('commande/affichercommandes.html.twig', [
        'commandes' => $commandes,
    ]);
}

#[Route('/suppCommande/{id}', name: 'suppC')]
    public function suppClassroom($id,CommandeRepository $com,
    ManagerRegistry $doctrine): Response
    { 
    
    //récupérer la commande à supprimer
    $commande=$com->find($id);
    $idPanier = $commande->getPanier()->getId();    
    //Action suppression
     $em =$doctrine->getManager();
     $em->remove($commande);
     $em->flush();
return $this->redirectToRoute('interface_affichage_commandes', ['idPanier' => $idPanier]);
}

#[Route('/suppCommandeJSON/{id}', name: 'suppCJSON')]
    public function suppCJSON($id,CommandeRepository $com,ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
    {   
    //récupérer la commande à supprimer
    $commande=$com->find($id);  
    //Action suppression
     $em =$doctrine->getManager();
     $em->remove($commande);
     $em->flush();
     $jsonContent = $Normalizer->normalize($commande,'json',['groups'=>'commande']);
     return new Response("Comande Supprimé avec succès".json_encode($jsonContent));
}

//Fonction qui retourne les commandes d'un user sous format json 
#[Route('/afficheCommandeJSON', name: 'app_AfficheCommandeJSON')]
public function CommandesJson(Request $request,PanierRepository $panierRep,UserRepository $ur, NormalizerInterface $Normalizer):Response
{   $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
    $panier = $panierRep->findOneBy(['user' => $user]);
    $idPanier=$panier->getId();
    $commandes = $this->getDoctrine()->getRepository(Commande::class)->findBy(['panier' => $idPanier]);

   
    $jsonContent = $Normalizer->normalize($commandes,'json',['groups'=>'commande']);
    return new Response(json_encode($jsonContent));
}


}
