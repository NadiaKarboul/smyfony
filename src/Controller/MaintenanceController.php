<?php

namespace App\Controller;

use App\Entity\Garagec;
use App\Entity\Maintenance;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MaintenanceFormType;
use App\Repository\MaintenanceRepository;
use App\Repository\GaragecRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MaintenanceController extends AbstractController
{
    #[Route('/maintenance', name: 'app_maintenance')]
    public function index(MaintenanceRepository $r,FlashyNotifier $flashy, ManagerRegistry $doctrine,GaragecRepository $r1): Response
    {  $user=$this->getUser();
        $maintenances = $r->findMaintenanceByIdUser($user);
        $test = false;
        $dateAujourdhui = new DateTime();
        $garageC = $r1->orderById();
        
        if ($maintenances) {
            foreach ($maintenances as $maintenance) {
                $diff = $dateAujourdhui->diff($maintenance->getDateMaintenance())->days;
                if ($diff <= 2) {
                    $test = true;
                   
                    break; // sortir de la boucle si une maintenance a été effectuée il y a moins de deux jours
                }
            }
        }
        
        if ($test) {
            // une maintenance a été effectuée il y a moins de deux jours
            $flashy->error('vous avez deja une maintenance . veuillez la modifier pour un nouveau devis!', 'http://your-awesome-link.com');
        } 
         //$dateLimite = $dateAujourdhui|date_modify('-2 days')|date('m/d/Y') ;
       
        return $this->render('front.html.twig', [
            't'=>$test,'g'=>$garageC
        ]);
    }
    #[Route('/afficheM', name: 'app_afficheM')]
    public function afficheM(MaintenanceRepository $r): Response
    {
        $maintenance=$r->orderById();
        return $this->render('maintenance/afficheM.html.twig', [
            'm'=>$maintenance,
        ]);
    }
    #[Route('/detailGCU/{id}', name: 'detailGCU')]
    public function detailGCU($id): Response
    {
        $garageC=$this->getDoctrine()->getRepository(Garagec::class)->find($id);
        return $this->render('garage_c/detailGCU.html.twig', [
            'g'=>$garageC,
        ]);
    }
    #[Route('/afficheMU', name: 'app_afficheMU')]
    public function afficheMU(MaintenanceRepository $r, ManagerRegistry $doctrine): Response
    {   $user=$this->getUser();
       // $maintenances=$r->findMaintenanceByIdUser(26)->getlastmod;
       $maintenance = $r->findLastMaintenanceByIdUser($user);
       // if (count($maintenances) > 0) {
        //    $maintenance = $maintenances[0];
       // } else {
          //  $maintenance = null;
       // }
        //$maintenance=$maintenances[0];
        return $this->render('maintenance/afficheMU.html.twig', [
            'm'=>$maintenance,
        ]);
    }
    #[Route('/supprimerM/{id}', name: 'supprimerM')]
    public function supprimerM($id,MaintenanceRepository $r, ManagerRegistry $doctrine): Response
    {   ///////recuperer la classroom a supp//////////
        $maintenance=$r->find($id);
         ////////supprimer/////////
        $em=$doctrine->getManager();/////persist()
        $em->remove($maintenance);/////remove()
        $em->flush(); ////////flush()
        return $this->redirectToRoute('app_afficheM',);
    }
    #[Route('/ajouterM', name: 'ajouterM')]
    public function ajouterM(MaintenanceRepository $r,FlashyNotifier $flashy,GaragecRepository $r1,ManagerRegistry $doctrine,Request $request): Response
    { 
        $user=$this->getUser();
        $maintenances = $r->findMaintenanceByIdUser($user);
        $test = false;
        $dateAujourdhui = new DateTime();
        $garageC = $r1->orderById();
        
        if ($maintenances) {
            foreach ($maintenances as $maintenance) {
                $diff = $dateAujourdhui->diff($maintenance->getDateMaintenance())->days;
                if ($diff <= 2) {
                    $test = true;
                   
                    break; // sortir de la boucle si une maintenance a été effectuée il y a moins de deux jours
                }
            }
        }
        
        if ($test) {
            // une maintenance a été effectuée il y a moins de deux jours
            $flashy->error('vous avez deja une maintenance . veuillez la modifier pour un nouveau devis!', 'http://your-awesome-link.com');
                return $this->redirectToRoute("app_home");
           
            } 

        $maintenance=new Maintenance();
        $form=$this->createForm(MaintenanceFormType::class,$maintenance);
        $form->handleRequest($request);
       // $flashy->error('cocher au moins une case', 'http://your-awesome-link.com');
        if($form->isSubmitted() && $form->isValid ()){
            $em =$doctrine->getManager() ;
            $userr=$this->getUser();
            $user=$this->getDoctrine()->getRepository(User::class)->find($userr);
            $maintenance->setUser($user);
            $maintenance->setDateMaintenance(new \DateTime('now'));
            if($form->get('panne_moteur')->getData() || $form->get('pompe_a_eau')->getData() || $form->get('patin')->getData() || $form->get('essuie_glace')->getData() || $form->get('radiateur')->getData() || $form->get('ventilateur')->getData() || $form->get('duride')->getData() || $form->get('fuite_d_huile')->getData() || $form->get('vidange')->getData() || $form->get('filtre')->getData() || $form->get('batterie')->getData() || $form->get('amortisseur')->getData() || $form->get('frein_main')->getData() || $form->get('feu_d_eclairage')->getData()){
            $em->persist($maintenance);
            $em->flush();
            return $this->redirectToRoute('deviss', ['id' => $maintenance->getId()]);}
            else{ $flashy->error('cocher au moins une case', 'http://your-awesome-link.com');}
          
            
         }
        
           // return $this->redirectToRoute('app_afficheMU',);
          
           return $this->renderForm("maintenance/ajoutMaintenance.html.twig",
            array("f"=>$form));
            
        }


            #[Route('/ajouterMjson/new', name: 'ajouterMjson')]
        public function ajouterjson(ManagerRegistry $doctrine,Request $request,NormalizerInterface $Normalizer,MaintenanceRepository $r,FlashyNotifier $flashy,GaragecRepository $r1): Response
        {
            $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
            $maintenances = $r->findMaintenanceByIdUser($user);
            $test = false;
            $dateAujourdhui = new DateTime();
           // $garageC = $r1->orderById();
            
            if ($maintenances) {
                foreach ($maintenances as $maintenance) {
                    $diff = $dateAujourdhui->diff($maintenance->getDateMaintenance())->days;
                    if ($diff <= 2) {
                        $test = true;
                       
                        break; // sortir de la boucle si une maintenance a été effectuée il y a moins de deux jours
                    }
                }
            }
            
            if ($test) {
                // une maintenance a été effectuée il y a moins de deux jours
                $flashy->error('vous avez deja une maintenance . veuillez la modifier pour un nouveau devis!', 'http://your-awesome-link.com');
                    return $this->redirectToRoute("app_home");
               
                } 
    
            $maintenance=new Maintenance();
           // $user=$this->getDoctrine()->getRepository(User::class)->find(28);
           
            $maintenance->setUser($user);
            $maintenance->setDateMaintenance(new \DateTime('now'));
            if($request->get('Duride')=='false'){$maintenance->setDuride(False);}
            else{$maintenance->setDuride(True);}
            if($request->get('Amortisseur')=='false'){ $maintenance->setAmortisseur(false);}
            else{ $maintenance->setAmortisseur(True);}
            if($request->get('Batterie')=='false'){ $maintenance->setBatterie(false);}
            else{ $maintenance->setBatterie(True);}
            if( $request->get('Essuie_glace')=='false'){ $maintenance->setEssuieGlace(false);}
            else{ $maintenance->setEssuieGlace(True);}
            if($request->get('Feu_d_eclairage')=='false'){ $maintenance->setFeuDEclairage(false);}
            else{  $maintenance->setFeuDEclairage(True);}
            if($request->get('Filtre')=='false'){ $maintenance->setFiltre(false);}
            else{ $maintenance->setFiltre(True);}
            if($request->get('Frein_main')=='false'){ $maintenance->setFreinMain(false);}
            else{  $maintenance->setFreinMain(True);}
            if($request->get('Fuite_d_huile')=='false'){ $maintenance->setFuiteDHuile(false);}
            else{  $maintenance->setFuiteDHuile(True);}
            if($request->get('Panne_moteur')=='false'){ $maintenance->setPanneMoteur(false);}
            else{ $maintenance->setPanneMoteur(True);}
            if($request->get('Patin')=='false'){$maintenance->setPatin(false);}
            else{$maintenance->setPatin(True);}
            if($request->get('Pompe_a_eau')=='false'){$maintenance->setPompeAEau(false);}
            else{$maintenance->setPompeAEau(True);}
            if($request->get('Radiateur')=='false'){$maintenance->setRadiateur(false);}
            else{$maintenance->setRadiateur(True);}
            if($request->get('Ventilateur')=='false'){ $maintenance->setVentilateur(false);}
            else{ $maintenance->setVentilateur(True);}
            if($request->get('Vidange')=='false'){
            $maintenance->setVidange(False);
            }else{$maintenance->setVidange(True);}
            $em =$doctrine->getManager() ;
             
                $em->persist($maintenance);
                $em->flush();
                
    
                $jsonContent = $Normalizer->normalize($maintenance,'json',['groups'=>'maintenance']);
                return new Response(json_encode($jsonContent));
            }

        #[Route('/modifierM/{id}', name: 'modifierM')]
        public function modifierM($id,MaintenanceRepository $r,ManagerRegistry $doctrine,Request $request,FlashyNotifier $flashy)
                
              { //récupérer la classroom à modifier
                $maintenance=$r->find($id);
            $form=$this->createForm(MaintenanceFormType::class,$maintenance);
             $form->handleRequest($request);
             if($form->isSubmitted() && $form->isValid ()){
            $em =$doctrine->getManager() ;
            if($form->get('panne_moteur')->getData() || $form->get('pompe_a_eau')->getData() || $form->get('patin')->getData() || $form->get('essuie_glace')->getData() || $form->get('radiateur')->getData() || $form->get('ventilateur')->getData() || $form->get('duride')->getData() || $form->get('fuite_d_huile')->getData() || $form->get('vidange')->getData() || $form->get('filtre')->getData() || $form->get('batterie')->getData() || $form->get('amortisseur')->getData() || $form->get('frein_main')->getData() || $form->get('feu_d_eclairage')->getData()){
                 $em->flush();
                 return $this->redirectToRoute('deviss', ['id' => $id]);}
          else{ $flashy->error('cocher au moins une case', 'http://your-awesome-link.com');} 
        
        }
            
           return $this->renderForm("maintenance/ajoutMaintenance.html.twig",
                                       array("f"=>$form));
                                
                            }


 #[Route('/afficheMjson', name: 'app_afficheMjson')]
    public function afficheMjson(MaintenanceRepository $r, NormalizerInterface $Normalizer): Response
    {
        $maintenance=$r->orderById();
        $jsonContent = $Normalizer->normalize($maintenance,'json',['groups'=>'maintenance']);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/detailGCUjson/{id}', name: 'detailGCUjson')]
    public function detailGCUjson($id,NormalizerInterface $Normalizer): Response
    {
        $garageC=$this->getDoctrine()->getRepository(Garagec::class)->find($id);
        $jsonContent = $Normalizer->normalize($garageC,'json',['groups'=>'maintenance']);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/afficheMUjson', name: 'app_afficheMUjson')]
    public function afficheMUjson(Request $request,MaintenanceRepository $r, ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
    {   
       // $maintenances=$r->findMaintenanceByIdUser(26)->getlastmod;
       $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
       $maintenance = $r->findLastMaintenanceByIdUser((int)$user->getIdUser());
       // if (count($maintenances) > 0) {
        //    $maintenance = $maintenances[0];
       // } else {
          //  $maintenance = null;
       // }
        //$maintenance=$maintenances[0];
        $jsonContent = $Normalizer->normalize($maintenance,'json',['groups'=>'maintenance']);
        return new Response(json_encode($jsonContent));
    }

}