<?php

namespace App\Controller;

use App\Entity\Devis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Garagec;
use App\Entity\Maintenance;
use App\Entity\User;
use App\Repository\MaintenanceRepository;
use App\Repository\DevisRepository;
use App\Repository\GaragecRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Email;
use Dompdf\Options;
use Dompdf\Dompdf;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DevisController extends AbstractController
{
    #[Route('/devis', name: 'app_devis')]
    public function index(): Response
    {
        return $this->render('devis/index.html.twig', [
            'controller_name' => 'DevisController',
        ]);
    }

    #[Route('/email/{id}', name: 'email')]
    public function sendEmail($id,TransportInterface $mailer,FlashyNotifier $flashy,UserRepository $ur,DevisRepository $r1,): Response
    {$userr=$this->getUser();
        $user = $ur->find($userr);
        $devis=$r1->find($id);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdf = new Dompdf($pdfOptions);
       
        //Contenu du Pdf
        $html = '<html><body>';
        $html .= ' <div style="background-color: red; height: 50px; display: flex; justify-content: center; align-items: center;">
        <span style="color: white; font-size: 24px; text-align:center">Drift&Race</span>
      </div>';
       // $html = '<img src="data:image/png;base64,' . base64_encode(file_get_contents('C:\xampp\htdocs\Hooligans-bon_plan_web\Hooligans-bon_plan_web\public\tete.png')) . '" alt="Image PNG">';
       $html .= '<h1>Devis</h1>';
       $html .= '<p>Vendeur : Drift$Race</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<p>Client : '.$user->getNom().' '.$user->getPrenom().'</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<p>Garage : '.$devis->getGarage()->getNomGarage().'</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<h3>Détails du devis</h3>';
        $html .= '<table border="1" cellspacing="0" style="width: 100%; text-align: center;">';
        $html .= '<tr style="background-color: black; color: white; font-weight: bold;"><th>Pannes</th><th>Prix</th></tr>';
        //$html .= '<table border="1" cellspacing="0">';
       // $html .= '<tr><th>Pannes</th><th>Prix</th></tr>';
       if($devis->getMaintenance()->isPanneMoteur()){
            $html .= '<tr><td>Panne Moteur</td><td>'.$devis->getGarage()->getPanneMoteur().'DT</td>';
       }
       if($devis->getMaintenance()->isFeuDEclairage()){
        $html .= '<tr><td>Feu D Eclairage</td><td>'.$devis->getGarage()->getFeuDEclairage().'DT</td>';
   }
   if($devis->getMaintenance()->isAmortisseur()){
    $html .= '<tr><td>Amortisseur</td><td>'.$devis->getGarage()->getAmortisseur().'DT</td>';
}
if($devis->getMaintenance()->isBatterie()){
    $html .= '<tr><td>Batterie</td><td>'.$devis->getGarage()->getBatterie().'DT</td>';
}
if($devis->getMaintenance()->isDuride()){
    $html .= '<tr><td>Duride</td><td>'.$devis->getGarage()->getDuride().'DT</td>';
}
if($devis->getMaintenance()->isEssuieGlace()){
    $html .= '<tr><td>EssuieGlace</td><td>'.$devis->getGarage()->getEssuieGlace().'DT</td>';
}
if($devis->getMaintenance()->isFiltre()){
    $html .= '<tr><td>Filtre</td><td>'.$devis->getGarage()->getFiltre().'DT</td>';
}
if($devis->getMaintenance()->isFreinMain()){
    $html .= '<tr><td>FreinMain</td><td>'.$devis->getGarage()->getFreinMain().'DT</td>';
}
if($devis->getMaintenance()->isFuiteDHuile()){
    $html .= '<tr><td>FuiteDHuile</td><td>'.$devis->getGarage()->getFuiteDHuile().'DT</td>';
}
if($devis->getMaintenance()->isPatin()){
    $html .= '<tr><td>Patin</td><td>'.$devis->getGarage()->getPatin().'DT</td>';
}
if($devis->getMaintenance()->isPompeAEau()){
    $html .= '<tr><td>PompeAEau</td><td>'.$devis->getGarage()->getPompeAEau().'DT</td>';
}
if($devis->getMaintenance()->isRadiateur()){
    $html .= '<tr><td>Radiateur</td><td>'.$devis->getGarage()->getRadiateur().'DT</td>';
}
if($devis->getMaintenance()->isVentilateur()){
    $html .= '<tr><td>Ventilateur</td><td>'.$devis->getGarage()->getVentilateur().'DT</td>';
}
if($devis->getMaintenance()->isVidange()){
    $html .= '<tr><td>Vidange</td><td>'.$devis->getGarage()->getVidange().'DT</td>';
}

     
        $html .= '</table>';
        $html .= '<p>Arretez la presente Piece apres une reduction de '.$devis->getGarage()->getTauxDeReduction().' % a la somme de : '.$devis->getTotal().'DT</p>';
        $html .= '<p>________________________________________________</p>';
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
        file_put_contents('C:\Users\helam\Desktop\helapdf\pdf.pdf', $output);
        
        // Rendre la template Twig en HTML
        $contenu = $this->renderView('devis/test.html.twig');
        
        // Créer l'email
        $email = (new Email())
            ->from('helamoalla91@gmail.com')
            ->to($user->getEmail())
            ->subject('DEVIS')
            ->html($contenu)
            ->attachFromPath('C:\Users\helam\Desktop\helapdf\pdf.pdf', 'pdf.pdf');
        
        // Envoyer l'email
        $mailer->send($email);
        $flashy->success('email envoyé', 'http://your-awesome-link.com');
       
      return $this->redirectToRoute('app_home',);
     
    }

    #[Route('/emailjson/{id}', name: 'emailjson')]
    public function sendEmailjson($id,Request $request,NormalizerInterface $Normalizer,TransportInterface $mailer,FlashyNotifier $flashy,UserRepository $ur,DevisRepository $r1,): Response
    {   $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
       //// $user = $ur->find(28);
         $devis=$r1->find($id);
        //$devis=$r1->find($id);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdf = new Dompdf($pdfOptions);
       
        //Contenu du Pdf
        $html = '<html><body>';
        $html .= ' <div style="background-color: red; height: 50px; display: flex; justify-content: center; align-items: center;">
        <span style="color: white; font-size: 24px; text-align:center">Drift&Race</span>
      </div>';
       // $html = '<img src="data:image/png;base64,' . base64_encode(file_get_contents('C:\xampp\htdocs\Hooligans-bon_plan_web\Hooligans-bon_plan_web\public\tete.png')) . '" alt="Image PNG">';
       $html .= '<h1>Devis</h1>';
       $html .= '<p>Vendeur : Drift$Race</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<p>Client : '.$user->getNom().' '.$user->getPrenom().'</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<p>Garage : '.$devis->getGarage()->getNomGarage().'</p>';
        $html .= '<p>________________________________________________</p>';
        $html .= '<h3>Détails du devis</h3>';
        $html .= '<table border="1" cellspacing="0" style="width: 100%; text-align: center;">';
        $html .= '<tr style="background-color: black; color: white; font-weight: bold;"><th>Pannes</th><th>Prix</th></tr>';
        //$html .= '<table border="1" cellspacing="0">';
       // $html .= '<tr><th>Pannes</th><th>Prix</th></tr>';
       if($devis->getMaintenance()->isPanneMoteur()){
            $html .= '<tr><td>Panne Moteur</td><td>'.$devis->getGarage()->getPanneMoteur().'DT</td>';
       }
       if($devis->getMaintenance()->isFeuDEclairage()){
        $html .= '<tr><td>Feu D Eclairage</td><td>'.$devis->getGarage()->getFeuDEclairage().'DT</td>';
   }
   if($devis->getMaintenance()->isAmortisseur()){
    $html .= '<tr><td>Amortisseur</td><td>'.$devis->getGarage()->getAmortisseur().'DT</td>';
}
if($devis->getMaintenance()->isBatterie()){
    $html .= '<tr><td>Batterie</td><td>'.$devis->getGarage()->getBatterie().'DT</td>';
}
if($devis->getMaintenance()->isDuride()){
    $html .= '<tr><td>Duride</td><td>'.$devis->getGarage()->getDuride().'DT</td>';
}
if($devis->getMaintenance()->isEssuieGlace()){
    $html .= '<tr><td>EssuieGlace</td><td>'.$devis->getGarage()->getEssuieGlace().'DT</td>';
}
if($devis->getMaintenance()->isFiltre()){
    $html .= '<tr><td>Filtre</td><td>'.$devis->getGarage()->getFiltre().'DT</td>';
}
if($devis->getMaintenance()->isFreinMain()){
    $html .= '<tr><td>FreinMain</td><td>'.$devis->getGarage()->getFreinMain().'DT</td>';
}
if($devis->getMaintenance()->isFuiteDHuile()){
    $html .= '<tr><td>FuiteDHuile</td><td>'.$devis->getGarage()->getFuiteDHuile().'DT</td>';
}
if($devis->getMaintenance()->isPatin()){
    $html .= '<tr><td>Patin</td><td>'.$devis->getGarage()->getPatin().'DT</td>';
}
if($devis->getMaintenance()->isPompeAEau()){
    $html .= '<tr><td>PompeAEau</td><td>'.$devis->getGarage()->getPompeAEau().'DT</td>';
}
if($devis->getMaintenance()->isRadiateur()){
    $html .= '<tr><td>Radiateur</td><td>'.$devis->getGarage()->getRadiateur().'DT</td>';
}
if($devis->getMaintenance()->isVentilateur()){
    $html .= '<tr><td>Ventilateur</td><td>'.$devis->getGarage()->getVentilateur().'DT</td>';
}
if($devis->getMaintenance()->isVidange()){
    $html .= '<tr><td>Vidange</td><td>'.$devis->getGarage()->getVidange().'DT</td>';
}

     
        $html .= '</table>';
        $html .= '<p>Arretez la presente Piece apres une reduction de '.$devis->getGarage()->getTauxDeReduction().' % a la somme de : '.$devis->getTotal().'DT</p>';
        $html .= '<p>________________________________________________</p>';
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
        file_put_contents('C:\Users\Nadia\Desktop\tests\testNadia\pdf.pdf', $output);
        
        // Rendre la template Twig en HTML
        $contenu = $this->renderView('devis/test.html.twig');
        
        // Créer l'email
        $email = (new Email())
            ->from('helamoalla91@gmail.com')
            ->to($user->getEmail())
            ->subject('DEVIS')
            ->html($contenu)
            ->attachFromPath('C:\Users\Nadia\Desktop\tests\testNadia\pdf.pdf', 'pdf.pdf');
        
        // Envoyer l'email
        $mailer->send($email);
        $flashy->success('email envoyé', 'http://your-awesome-link.com');
       
        return $this->redirectToRoute('app_home',);
     
    }

    #[Route('/deviss/{id}', name: 'deviss')]
    public function devis($id,MaintenanceRepository $r,GaragecRepository $r1,ManagerRegistry $doctrine,Request $request): Response
    {  
        $m=$r->find($id);
        $devis = array(); // un tableau pour stocker les devis générés
        $TTC = array(); 
        $TVA = 19;
        $g = $r1->findAll();
        
        for($i=0; $i<count($g); $i++){
            $devis[$i]= new Devis();
            $somme=0;
        
            if ($m->isFeuDEclairage()) {
                $somme = $somme + $g[$i]->getFeuDEclairage();
            }
            if ($m->isAmortisseur()) {
                $somme = $somme + $g[$i]->getAmortisseur();
            }
            if ($m->isBatterie()) {
                $somme = $somme + $g[$i]->getBatterie();
            }
            if ($m->isDuride()) {
                $somme = $somme + $g[$i]->getDuride();
            }
            if ($m->isEssuieGlace()) {
                $somme = $somme + $g[$i]->getEssuieGlace();
            }
            if ($m->isFiltre()) {
                $somme = $somme + $g[$i]->getFiltre();
            }
            if ($m->isFreinMain()) {
                $somme = $somme + $g[$i]->getFreinMain();
            }
            if ($m->isFuiteDHuile()) {
                $somme = $somme + $g[$i]->getFuiteDHuile();
            }
            if ($m->isPanneMoteur()) {
                $somme = $somme + $g[$i]->getPanneMoteur();
            }
            if ($m->isPatin()) {
                $somme = $somme + $g[$i]->getPatin();
            }
            if ($m->isPompeAEau()) {
                $somme = $somme + $g[$i]->getPompeAEau();
            }
            if ($m->isRadiateur()) {
                $somme = $somme + $g[$i]->getRadiateur();
            }
            if ($m->isVentilateur()) {
                $somme = $somme + $g[$i]->getVentilateur();
            }
            if ($m->isVidange()) {
                $somme = $somme + $g[$i]->getVidange();
            }
            $user=$this->getUser();
            $devis[$i]->setGarage($g[$i]);
            $devis[$i]->setMaintenance($m);
            $devis[$i]->setUser($user);  
            $devis[$i]->setTVA($TVA);
            $T = ($somme * $TVA) / 100;
            $TTC[$i] = ($T + $somme);
            $Red = $TTC[$i] - ($TTC[$i] * $g[$i]->getTauxDeReduction()) / 100;
            $devis[$i]->setTotal($Red);
        
        $em =$doctrine->getManager() ;
        $em->persist($devis[$i]);
        $em->flush();
    }
        return $this->render('devis/index.html.twig', [
            'd' => $devis,'TTC' => $TTC , 'g'=>$g,
        ]);
    }

    #[Route('/devissjson', name: 'devissjson')]
    public function devisjson(MaintenanceRepository $r,GaragecRepository $r1,ManagerRegistry $doctrine,NormalizerInterface $Normalizer,Request $request): Response
    {       
        $m=$r->find((int)$request->get('id'));
        $devis = array(); // un tableau pour stocker les devis générés
        $TTC = array(); 
        $TVA = 19;
        $g = $r1->findAll();
        
        for($i=0; $i<count($g); $i++){
            $devis[$i]= new Devis();
            $somme=0;
        
            if ($m->isFeuDEclairage()) {
                $somme = $somme + $g[$i]->getFeuDEclairage();
            }
            if ($m->isAmortisseur()) {
                $somme = $somme + $g[$i]->getAmortisseur();
            }
            if ($m->isBatterie()) {
                $somme = $somme + $g[$i]->getBatterie();
            }
            if ($m->isDuride()) {
                $somme = $somme + $g[$i]->getDuride();
            }
            if ($m->isEssuieGlace()) {
                $somme = $somme + $g[$i]->getEssuieGlace();
            }
            if ($m->isFiltre()) {
                $somme = $somme + $g[$i]->getFiltre();
            }
            if ($m->isFreinMain()) {
                $somme = $somme + $g[$i]->getFreinMain();
            }
            if ($m->isFuiteDHuile()) {
                $somme = $somme + $g[$i]->getFuiteDHuile();
            }
            if ($m->isPanneMoteur()) {
                $somme = $somme + $g[$i]->getPanneMoteur();
            }
            if ($m->isPatin()) {
                $somme = $somme + $g[$i]->getPatin();
            }
            if ($m->isPompeAEau()) {
                $somme = $somme + $g[$i]->getPompeAEau();
            }
            if ($m->isRadiateur()) {
                $somme = $somme + $g[$i]->getRadiateur();
            }
            if ($m->isVentilateur()) {
                $somme = $somme + $g[$i]->getVentilateur();
            }
            if ($m->isVidange()) {
                $somme = $somme + $g[$i]->getVidange();
            }
           // $user=$this->getUser();
           $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
            $devis[$i]->setGarage($g[$i]);
            $devis[$i]->setMaintenance($m);
            $devis[$i]->setUser($user);  
            $devis[$i]->setTVA($TVA);
            $T = ($somme * $TVA) / 100;
            $TTC[$i] = ($T + $somme);
            $Red = $TTC[$i] - (($TTC[$i] * $g[$i]->getTauxDeReduction()) / 100);
            $devis[$i]->setTotal($Red);
        
        $em =$doctrine->getManager() ;
        $em->persist($devis[$i]);
        $em->flush();
    }
    $jsonContent = $Normalizer->normalize($devis,'json',['groups'=>'devis']);
    return new Response(json_encode($jsonContent));
    
    }
}