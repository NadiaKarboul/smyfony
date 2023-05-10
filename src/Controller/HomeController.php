<?php
namespace App\Controller;

use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCode\Generator\QrCodeGeneratorInterface;
use Dompdf\Dompdf;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Endroid\QrCode\QrCode;
use App\Entity\Event;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EventType;
use App\Form\QuantiteType;
use App\Repository\EventRepository;
use App\Repository\TicketRepository;
use App\Service\SagendaAPI;
use BaconQrCode\Common\ErrorCorrectionLevel;
use InvalidArgumentException;
use PHPUnit\Framework\Constraint\FileExists;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\QrCodeGenerator;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;
use App\Repository\UserRepository;
use App\Repository\BonplanRepository;
use App\Repository\GaragecRepository;
use App\Repository\FeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;

class HomeController extends AbstractController
{ ////////////////haaajjj///////////
    #[Route('/home', name: 'app_home')]
    public function indexx(GaragecRepository $r1,FlashyNotifier $flashy,EventRepository $eventRep,BonplanRepository $bonplanRep,FeedbackRepository $feedRep): Response
    {  $garageC = $r1->orderById();
        $allBonplan = $bonplanRep->getAllBonPlanWithFeedbacks();
        $allFeeds=$feedRep->findAll();
        $allEvents=$eventRep->findAll();

       // $flashy->error('vous avez deja une maintenance . veuillez la modifier pour un nouveau devis!', 'http://your-awesome-link.com');
        return $this->render('base.html.twig', [
            'controller_name' => 'HomeController',
            'allBonplan'=>$allBonplan,
            'allFeeds'=>$allFeeds,
            'allEvents'=>$allEvents,
            'g'=>$garageC
        ]);
    }
//////////////////ayouuuubbb//////////
public function index(): Response
    {
        return $this->render('admin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
  
    #[Route('/qrcode', name: 'qrcode')]
    public function index1(): Response
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create('nom event')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(120)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $logo = Logo::create('images/logo.png')
            ->setResizeToWidth(60);
        $label = Label::create('')->setFont(new NotoSans(8));
 
        $qrCodes = [];
        $qrCodes['img'] = $writer->write($qrCode, $logo)->getDataUri();
        $qrCodes['simple'] = $writer->write(
                                $qrCode,
                                null,
                                $label->setText('Simple')
                            )->getDataUri();
 
        $qrCode->setForegroundColor(new Color(255, 0, 0));
        $qrCodes['changeColor'] = $writer->write(
            $qrCode,
            null,
            $label->setText('Color Change')
        )->getDataUri();
 
        $qrCode->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 0, 0));
        $qrCodes['changeBgColor'] = $writer->write(
            $qrCode,
            null,
            $label->setText('Background Color Change')
        )->getDataUri();
 
        $qrCode->setSize(200)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
        $qrCodes['withImage'] = $writer->write(
            $qrCode,
            $logo,
            $label->setText('With Image')->setFont(new NotoSans(20))
        )->getDataUri();
 
        return $this->render('qrcode.html.twig', $qrCodes);
    

    }


    #[Route('/add', name: 'add')]
    public function ajouter(ManagerRegistry $doctrine,Request $request): Response
    {
        $Event=new Event();
        
        $form=$this->createForm(EventType::class,$Event);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $em =$doctrine->getManager() ;
            $imageFile = $form->get('image_event')->getData();
            
            if ($imageFile) {
                $imagesDirectory = 'C:/xampp/htdocs/images';
                $originalFilename = $imageFile->getClientOriginalName();
                $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);

                
                    
                    $imageFile->move($imagesDirectory,$filenameWithoutSpaces);
           
                $Event->setImageEvent($filenameWithoutSpaces);
            }
            

            //$bonplan->setImage("img.jpg");
         
            $em->persist($Event);
            $em->flush();
            return $this->redirectToRoute('app_get');}

           return $this->renderForm("Event/addEvent.html.twig",
           
            array("f"=>$form));
        }
        #[Route('/get', name: 'app_get')]
    public function affiche(EventRepository $r): Response
        {
        $Event=$r->orderById();
        return $this->render('Event/getallEvent.html.twig', [
            'e'=>$Event,
        ]);
    }
    #[Route('/getjson', name: 'app_getjson')]
    public function affichejson(EventRepository $r,NormalizerInterface $Normalizer): Response
        {
        $Event=$r->orderById();
        $jsonContent = $Normalizer->normalize($Event,'json',['groups'=>'event']);
return new Response(json_encode($jsonContent));
    }
    #[Route('/resume', name: 'resume')]
    public function resume(): Response
    {
        return $this->render('resume.html.twig', [
            
        ]);
    }
    #[Route('/supprimer/{id}', name: 'supprimer')]
    public function supprimer($id,EventRepository $r, ManagerRegistry $doctrine): Response
    {   ///////recuperer la classroom a supp//////////
         $Event=$r->find($id);
         ////////supprimer/////////
        $em=$doctrine->getManager();/////persist()
        $em->remove($Event);/////remove()
        $em->flush(); ////////flush()
        return $this->redirectToRoute('app_get',);
    }
    #[Route('/supprimerjson/{id}', name: 'supprimerjson')]
    public function supprimerjson($id,EventRepository $r, ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
    {   ///////recuperer la classroom a supp//////////
         $Event=$r->find($id);
         ////////supprimer/////////
        $em=$doctrine->getManager();/////persist()
        $em->remove($Event);/////remove()
        $em->flush(); ////////flush()
        $jsonContent = $Normalizer->normalize($Event,'json',['groups'=>'event']);
        return new Response("event Supprimé avec succès".json_encode($jsonContent));
    }
    #[Route('/modifier/{id}', name: 'modifier')]
        public function modifier($id,EventRepository $r,ManagerRegistry $doctrine,Request $request,TransportInterface $mailer,UserRepository $ur,TicketRepository $tr )
                
              { //récupérer Event à modifier
 
                
                                
                    $Event=$r->find($id);
                    $form=$this->createForm(EventType::class,$Event);
                     $form->handleRequest($request);
        
                     
                     $imageFile = $form->get('image_event')->getData();
                    
                  
                     if($form->isSubmitted() && $form->isValid()){
                    $em =$doctrine->getManager() ;
                    $imageFile = $form->get('image_event')->getData();
        
                    if ($imageFile) {
                        
                        $imagesDirectory = 'C:/xampp/htdocs/images';
                        $originalFilename = $imageFile->getClientOriginalName();
                        $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);
        
                        
                           
                           $imageFile->move($imagesDirectory, $filenameWithoutSpaces);
                      
                        
                        $Event->setImageEvent($filenameWithoutSpaces);
                        }
                        $em->flush();
                        // Créer l'email
                      
                        $tickets = $tr->findBy([
                            'event' => $id
                        ]);

                        foreach ($tickets as $ticket){
                            $email = (new Email())
                            ->from('ayoub.bbarnat@gmail.com')
                            ->to($ticket->getUser()->getEmail())
                            ->subject('Reprogrammation de l evenement')
                            ->text('La date de l\'événement a été modifiée. L\'événement sera programmé pour le ' . $Event->getDateDebut()->Format('d/m/Y') . ' Et se termine le  ' . $Event->getDateFin()->Format('d/m/Y') );
                        
                        // Envoyer l'email
                        $mailer->send($email);
                        }

       
                        return $this->redirectToRoute("app_get");
                    }
                                                         
                
                    return $this->renderForm("Event/addEvent.html.twig",
                                               array("f"=>$form)); 
                                               

                
         
                                
                            }
                            #[Route('/getU', name: 'app_getU')]
                            public function afficheU(EventRepository $r): Response
                                {
                                    $Event=$r->orderById();
                                return $this->render('Event/getallEventUser.html.twig', [
                                    'e'=>$Event,
                                ]);
                            }
                            #[Route('/getUjson', name: 'app_getUjson')]
                            public function afficheUjson(EventRepository $r,NormalizerInterface $Normalizer): Response
                                {
                                    $Event=$r->orderById();
                                    $jsonContent = $Normalizer->normalize($Event,'json',['groups'=>'event']);
                                    return new Response(json_encode($jsonContent));
                            }
                            #[Route('/detailE/{id}', name: 'app_detail')]
                            public function detailE($id,Request $request,ManagerRegistry $doctrine): Response
                                {
                                    $form=$this->createForm(QuantiteType::class);
                                    $form->handleRequest($request);
                                  
                                $Event=$this->getDoctrine()->getRepository(Event::class)->find($id);
                                if($form->isSubmitted() && $form->isValid()){
                                    $user=$this->getUser();
                                    $quantite= $form->get('quantity')->getData();
                                    $montant=$quantite*($Event->getPrixEvent());
                                    if($montant>$user->getQuota())
                             {$this->addFlash('error1', 'solde insuffisant');}
                                    if($montant<$user->getQuota()){
                                    for ($i = 0; $i < $quantite; $i++) {
                                        $Event=$this->getDoctrine()->getRepository(Event::class)->find($id);
                                        $ticket=new Ticket();
                                        $userr=$this->getUser();
                                        $user=$this->getDoctrine()->getRepository(User::class)->find($userr);
                                        $ticket->setUser($user);
                                        $ticket->setEvent($Event);
                                        $ticket->setNumTicket(123);
                                        $writer = new PngWriter();
                                       
                                        $qrCode = QrCode::create( "nom evenement : ".$Event->getNomEvent().", lieu : ".$Event->getLieuEvent())
                                            ->setEncoding(new Encoding('UTF-8'))
                                            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                                            ->setSize(120)
                                            ->setMargin(0)
                                            ->setForegroundColor(new Color(0, 0, 0))
                                            ->setBackgroundColor(new Color(255, 255, 255));
                                 
                                        
                                 
                                        $qrCodes = [];
                                      
                                        $qrCodes['simple'] = $writer->write(
                                                                $qrCode,
                                                                null,
                                                          
                                                            )->getDataUri();
                                       
                                    
                                        $chemin ='C:/xampp/htdocs/images/';

//Générer un nom unique pour l'image
$nomImage = uniqid('qr_', true) . '.png';

//Enregistrer l'image sur le disque
file_put_contents($chemin . $nomImage, base64_decode(substr($qrCodes['simple'], strpos($qrCodes['simple'], ',') + 1)));

//Enregistrer le nom de l'image dans la base de données
$ticket->setImageQr($nomImage);
                                        $em =$doctrine->getManager() ;
                                      



$user->setQuota($user->getQuota()-$montant);
                                          $em->persist($ticket);
                                          
                                          $em->flush();
                              }
                                        
                              return $this->redirectToRoute("app_getT");}
                            else if($montant==$user->getQuota()){
                                for ($i = 0; $i < $quantite; $i++) {
                                    $Event=$this->getDoctrine()->getRepository(Event::class)->find($id);
                                    $ticket=new Ticket();
                                    $userr=$this->getUser();
                                    $user=$this->getDoctrine()->getRepository(User::class)->find($userr);
                                    $ticket->setUser($user);
                                    $ticket->setEvent($Event);
                                    $ticket->setNumTicket(123);
                                    $writer = new PngWriter();
                                   
                                    $qrCode = QrCode::create( $Event->getNomEvent())
                                        ->setEncoding(new Encoding('UTF-8'))
                                        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                                        ->setSize(120)
                                        ->setMargin(0)
                                        ->setForegroundColor(new Color(0, 0, 0))
                                        ->setBackgroundColor(new Color(255, 255, 255));
                             
                                    
                             
                                    $qrCodes = [];
                                  
                                    $qrCodes['simple'] = $writer->write(
                                                            $qrCode,
                                                            null,
                                                      
                                                        )->getDataUri();
                                   
                                
                                    $chemin ='C:/xampp/htdocs/images/';

//Générer un nom unique pour l'image
$nomImage = uniqid('qr_', true) . '.png';

//Enregistrer l'image sur le disque
file_put_contents($chemin . $nomImage, base64_decode(substr($qrCodes['simple'], strpos($qrCodes['simple'], ',') + 1)));

//Enregistrer le nom de l'image dans la base de données
$ticket->setImageQr($nomImage);
                                    $em =$doctrine->getManager() ;
                                  



$user->setQuota(0);
                                      $em->persist($ticket);
                                      
                                      $em->flush();
                          }
                                    
                          return $this->redirectToRoute("app_getT");

                            }
                            
                            }
                             
                              
                            
                                return $this->renderForm("Event/detailEvent.html.twig",
                                array("f"=>$form,"e"=>$Event));
                            }
                            #[Route('/detailEjson/{id}', name: 'app_detailjson')]
                            public function detailEjson($id,Request $request,ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
                                {
                                    $form=$this->createForm(QuantiteType::class);
                                    $form->handleRequest($request);
                                $Event=$this->getDoctrine()->getRepository(Event::class)->find($id);
                                if($form->isSubmitted() && $form->isValid()){
                                   
                                    $quantite= $form->get('quantity')->getData();
                        
                                    for ($i = 0; $i < $quantite; $i++) {
                                        $Event=$this->getDoctrine()->getRepository(Event::class)->find($id);
                                        $ticket=new Ticket();
                                        $userr=$this->getUser();
                                        $user=$this->getDoctrine()->getRepository(User::class)->find($userr);
                                        $ticket->setUser($user);
                                        $ticket->setEvent($Event);
                                        $ticket->setNumTicket(123);
                                        $writer = new PngWriter();
                                       
                                        $qrCode = QrCode::create( $Event->getNomEvent())
                                            ->setEncoding(new Encoding('UTF-8'))
                                            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                                            ->setSize(120)
                                            ->setMargin(0)
                                            ->setForegroundColor(new Color(0, 0, 0))
                                            ->setBackgroundColor(new Color(255, 255, 255));
                                 
                                        
                                 
                                        $qrCodes = [];
                                      
                                        $qrCodes['simple'] = $writer->write(
                                                                $qrCode,
                                                                null,
                                                          
                                                            )->getDataUri();
                                       
                                    
                                        $chemin ='C:/xampp/htdocs/images/';

//Générer un nom unique pour l'image
$nomImage = uniqid('qr_', true) . '.png';

//Enregistrer l'image sur le disque
file_put_contents($chemin . $nomImage, base64_decode(substr($qrCodes['simple'], strpos($qrCodes['simple'], ',') + 1)));

//Enregistrer le nom de l'image dans la base de données
$ticket->setImageQr($nomImage);
                                        $em =$doctrine->getManager() ;

                                          $em->persist($ticket);
                                          
                                          $em->flush();
                              }
                                        
                              return $this->redirectToRoute("app_getTjson");}

                                
                                $jsonContent = $Normalizer->normalize($Event,'json',['groups'=>'event']);
                                return new Response(json_encode($jsonContent));
                            }
                            #[Route('/getT', name: 'app_getT')]
                            public function afficheT(TicketRepository $r): Response
                                {
                                $user=$this->getUser();
                                $Ticket = $r-> findByIdUser($user->getIdUser());
                                return $this->render('ticket/getallticket.html.twig', [
                                    't'=>$Ticket,
                                ]);
                            }
                            #[Route('/getTjson', name: 'app_getTjson')]
                            public function afficheTjson(Request $request,TicketRepository $r,NormalizerInterface $Normalizer): Response
                                {
                             ;
                                $Ticket = $r->findBy(['id_spectateur' => $request-> get("id_user")]);
                                $jsonContent = $Normalizer->normalize($Ticket,'json',['groups'=>'ticket']);
                                
                                return new Response(json_encode($jsonContent));
                            }
                            #[Route('/getTA', name: 'app_getTA')]
                            public function afficheTA(TicketRepository $r): Response
                                {
                                $Ticket=$r->orderById();
                                return $this->render('ticket/getallticketAdmin.html.twig', [
                                    't'=>$Ticket,
                                ]);
                            }
                            #[Route('/getTAjson', name: 'app_getTAjson')]
                            public function afficheTAjson(TicketRepository $r,NormalizerInterface $Normalizer): Response
                                {
                                $Ticket=$r->findAll();
                                $jsonContent = $Normalizer->normalize($Ticket,'json',['groups'=>'ticket']);
                                return new Response(json_encode($jsonContent));
                            }
                            #[Route('/supprimerTA/{id}', name: 'supprimerTA')]
                            public function supprimerTA($id,TicketRepository $r, ManagerRegistry $doctrine): Response
                            {   ///////recuperer la classroom a supp//////////
                                 $Ticket=$r->find($id);
                                 ////////supprimer/////////
                                $em=$doctrine->getManager();/////persist()
                                $em->remove($Ticket);/////remove()
                                $em->flush(); ////////flush()
                                return $this->redirectToRoute('app_getTA',);
                            }
                            #[Route('/supprimerTAjson/{id}', name: 'supprimerTAjson')]
                            public function supprimerTAjson($id,TicketRepository $r, ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
                            {   ///////recuperer la classroom a supp//////////
                                 $Ticket=$r->find($id);
                                 ////////supprimer/////////
                                $em=$doctrine->getManager();/////persist()
                                $em->remove($Ticket);/////remove()
                                $em->flush(); ////////flush()
                                $jsonContent = $Normalizer->normalize($Ticket,'json',['groups'=>'ticket']);
                                return new Response("ticket Supprimé avec succès".json_encode($jsonContent));
                            }
                            #[Route('/agenda', name: 'agenda')]

                            public function Agenda(EventRepository $r): Response
                            {
                               
                                $Event=$r->orderById();
                                return $this->render('Event/programme.html.twig', [
                                    'events' => $Event,
                                ]);
                            }
                            public function downloadTicketPdf($id)
{
    // Récupérer le ticket correspondant à l'ID
    $ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);

    // Générer le contenu du fichier PDF à télécharger (par exemple, le texte du ticket)
    $content = $this->renderView('ticket/pdf.html.twig', ['ticket' => $ticket]);

    // Générer le PDF avec Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($content);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Créer une réponse de type 'Response' avec le contenu PDF et les en-têtes appropriés
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="ticket.pdf"');

    return $response;
}


#[Route('/payerJSON', name: 'payerJSON')]
public function payerJSON(Request $request,EventRepository $bonplanRep, ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
{
$Event=$this->getDoctrine()->getRepository(Event::class)->find($request-> get("id_event"));
$ticket=new Ticket();
$userr=$this->getUser();
$user=$this->getDoctrine()->getRepository(User::class)->find($userr);
$ticket->setUser($user);
$ticket->setEvent($Event);
$ticket->setNumTicket(123);
$writer = new PngWriter();

$qrCode = QrCode::create( "nom evenement : ".$Event->getNomEvent().", lieu : ".$Event->getLieuEvent())
    ->setEncoding(new Encoding('UTF-8'))
    ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
    ->setSize(120)
    ->setMargin(0)
    ->setForegroundColor(new Color(0, 0, 0))
    ->setBackgroundColor(new Color(255, 255, 255));



$qrCodes = [];

$qrCodes['simple'] = $writer->write(
                        $qrCode,
                        null,
                  
                    )->getDataUri();


$chemin ='C:/xampp/htdocs/images/';

//Générer un nom unique pour l'image
$nomImage = uniqid('qr_', true) . '.png';

//Enregistrer l'image sur le disque
file_put_contents($chemin . $nomImage, base64_decode(substr($qrCodes['simple'], strpos($qrCodes['simple'], ',') + 1)));

//Enregistrer le nom de l'image dans la base de données
$ticket->setImageQr($nomImage);
$em =$doctrine->getManager() ;


$user->setQuota($user->getQuota()-1);
  $em->persist($ticket);
  
  $em->flush();

  $jsonContent = $Normalizer->normalize($ticket,'json',['groups'=>'ticket']);
  return new Response(json_encode($jsonContent));
}


#[Route('/ayoubJSON', name: 'ayoubJSON')]
public function ayoubJSON(Request $request,EventRepository $bonplanRep, ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
{
$Event=$this->getDoctrine()->getRepository(Event::class)->find($request-> get("id_event"));
$ticket=new Ticket();
//$userr=$this->getUser();
$user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
$ticket->setUser($user);
$ticket->setEvent($Event);
$ticket->setNumTicket(123);
$writer = new PngWriter();

$qrCode = QrCode::create( "nom evenement : ".$Event->getNomEvent().", lieu : ".$Event->getLieuEvent())
    ->setEncoding(new Encoding('UTF-8'))
    ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
    ->setSize(120)
    ->setMargin(0)
    ->setForegroundColor(new Color(0, 0, 0))
    ->setBackgroundColor(new Color(255, 255, 255));



$qrCodes = [];

$qrCodes['simple'] = $writer->write(
                        $qrCode,
                        null,
                  
                    )->getDataUri();


$chemin ='C:/xampp/htdocs/images/';

//Générer un nom unique pour l'image
$nomImage = uniqid('qr_', true) . '.png';

//Enregistrer l'image sur le disque
file_put_contents($chemin . $nomImage, base64_decode(substr($qrCodes['simple'], strpos($qrCodes['simple'], ',') + 1)));

//Enregistrer le nom de l'image dans la base de données
$ticket->setImageQr($nomImage);
$em =$doctrine->getManager() ;


$user->setQuota($user->getQuota()-1);
  $em->persist($ticket);
  
  $em->flush();

  $jsonContent = $Normalizer->normalize(['ticket'=>$ticket],'json',['groups'=>'ticket']);
  return new Response(json_encode($jsonContent));
}

#[Route('/pdfJson', name: 'PDFJSON')]
public function downloadTicketPdfjson(Request $request,NormalizerInterface $Normalizer,TransportInterface $mailer){
  //  $user=$this->getDoctrine()->getRepository(User::class)->find($request-> get("id_user"));
$ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($request-> get("id_ticket"));

// Générer le contenu du fichier PDF à télécharger (par exemple, le texte du ticket)
$content = $this->renderView('ticket/pdf.html.twig', ['ticket' => $ticket]);

// Générer le PDF avec Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($content);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
        // Render the PDF as a string
        $dompdf->render();
        // Retourner le pdf
        $output = $dompdf->output();
file_put_contents('C:\Users\Nadia\Desktop\tests\ticket.pdf', $output);
// Créer une réponse de type 'Response' avec le contenu PDF et les en-têtes appropriés
$response = new Response($dompdf->output());
$response->headers->set('Content-Type', 'application/pdf');
$response->headers->set('Content-Disposition', 'attachment; filename="ticket.pdf"');
 // Créer l'email
 $email = (new Email())
 ->from('helamoalla91@gmail.com')
 ->to($ticket->getUser()->getEmail())
 ->subject('DEVIS')
 ->html($content)
 ->attachFromPath('C:\Users\Nadia\Desktop\tests\ticket.pdf', 'ticket.pdf');
 // Envoyer l'email
 $mailer->send($email);
 return $this->redirectToRoute('app_home',);
}
}
