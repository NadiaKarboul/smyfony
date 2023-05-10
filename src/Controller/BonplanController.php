<?php

namespace App\Controller;
use App\Entity\Bonplan;
use App\Entity\Feedback;
use App\Entity\User;
use App\Form\BonplanFormType;
use App\Form\FeedbackFormType;
use App\Form\SearchType;
use App\Repository\BonplanRepository;
use App\Repository\FeedbackRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Attachment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class BonplanController extends AbstractController
{
    #[Route('/allBonplan', name: 'all_bonplan')]
    public function allBonplan(BonplanRepository $bonplanRep, Request $request,PaginatorInterface $paginator,UserRepository $userRep ): Response
    {
        $user=$this->getUser();
        $recentBonplan = $bonplanRep->getRecentWithFeedbacks();

        if ($request->isMethod("POST")) {
            $name = $request->get('search');
            $allBonplan = $bonplanRep->search($name);
        } else {
            $allBonplan = $bonplanRep->getAllBonPlanWithFeedbacks();
        }

        $allBonplan = $paginator->paginate(
            $allBonplan, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,

            6/*limit per page*/,
           
        );
       
        $pageNumber = $allBonplan->getCurrentPageNumber(); 

        return $this->render('bonplan/allBonplan.html.twig', [
            'allBonplan' => $allBonplan,
            'recent'=>$recentBonplan,
            'pagenum'=>$pageNumber ,
            'user'=>$user
        ]);
    }
// //afficher bonplan json
//     #[Route('/admAllBonplanJson', name: 'admin_all_bonplanjson')]
//     public function adminAllBonplanJson(BonplanRepository $bonplanRep ,UserRepository $userRep,NormalizerInterface $Normalizer): Response
//     {
//         $allBonplan = $bonplanRep->orderById();
//         $user=$this->getUser();
//         $jsonContent = $Normalizer->normalize($allBonplan,'json',['groups'=>'bonplan']);
//         return new Response(json_encode($jsonContent));
           
//     }

    #[Route('/bonplan/{id}', name: 'detail_bonplan')]
    public function detailBonPlan(ManagerRegistry $doctrine, Request $request, UserRepository $userRep, BonplanRepository $bonplanRep, $id, FeedbackRepository $feedRep,TransportInterface $mailer): Response
    {

        $bonplan = $bonplanRep->find($id);
        $bonplanDetail = $bonplanRep->getBonPlanWithFeedbacks($bonplan);
        $feeds = $feedRep->getFeedbackByBonPlan($bonplan);
        $user=$this->getUser();
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackFormType::class, $feedback);

        if ($bonplanRep->checkIfAlreadyReported($bonplan, $user) > 0) {
            $form->add('report', null, [
                'disabled' => true,
            ]);
            $feedback->setReport(false);
        }
        if ($bonplanRep->checkIfAlreadyRated($bonplan, $user) > 0) {
            $form->add('rate', null, [
                'disabled' => true,
                'attr' => [
                    'style' => 'display:none',
                ],
            ]);
            $feedback->setRate(-1);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $feedback->setUser($user);
            $feedback->setBonplan($bonplan);

            $em->persist($feedback);
            $em->flush();

            // bonplan reporting
            $reportLines = $bonplanRep->count_reports();
            foreach ($reportLines as $line) {
                $bonplanReported = $line[0]->getBonplan();
                $reportCount = $line['report_count'];
                if ($reportCount >= 5) {
                    $email = (new Email())
                        ->from('hooligans.pidev@gmail.com')
                        ->to($bonplanReported->getUser()->getEmail())
                        ->subject('Be careful !')
                        ->text("Your bonplan ". $bonplanReported->getNomBonplan() ." has been deleted due to mutliple reports  !");
                        //->html('<p>See Twig integration for better HTML integration!</p>');

                    $mailer->send($email);
                    $em->remove($bonplanReported);
                    $em->flush();
                    return $this->redirectToRoute("all_bonplan");
                }
            }

            return $this->redirectToRoute("detail_bonplan", ["id" => $id]);
        }

        return $this->renderForm("bonplan/detailBonplan.html.twig", [
            "f" => $form,
            'bonplan' => $bonplanDetail,
            'feeds' => $feeds,
            'user'=>$user
        ]);
    }

    /// afficher detail bonplan Json
//     #[Route('/bonplanJson/{id}', name: 'detail_bonplan')]
//     public function detailBonPlanJson(ManagerRegistry $doctrine, Request $request, UserRepository $userRep, BonplanRepository $bonplanRep, $id, FeedbackRepository $feedRep,TransportInterface $mailer,NormalizerInterface $Normalizer): Response
//     {
//         $bonplan = $bonplanRep->find($id);
              
//    $jsonContent = $Normalizer->normalize($bonplan,'json',['groups'=>'bonplan']);
//    return new Response(json_encode($jsonContent));
       
//     }



      
    
    



    // ajouter bonPlan

    #[Route('/addBonplan', name: 'addBonplan')]
    public function addBonplan(ManagerRegistry $doctrine, Request $request, UserRepository $userRep): Response
    {
        $user=$this->getUser();

        $bonplan = new Bonplan();
        $form = $this->createForm(BonplanFormType::class, $bonplan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $bonplan->setUser($user);
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $imagesDirectory = 'C:/xampp/htdocs/images';
                $originalFilename = $imageFile->getClientOriginalName();
                $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);

                try {
                    $imageFile->move($imagesDirectory, $filenameWithoutSpaces);
                } catch (FileException $e) {
                    // Handle exception
                }

                $bonplan->setImage($filenameWithoutSpaces);
            }



            //$bonplan->setImage("img.jpg");
            $bonplan->setEtat('en attente');
            $em->persist($bonplan);
            $em->flush();
            return $this->redirectToRoute("all_bonplan");
        }

        return $this->renderForm(
            "bonplan/addBonplan.html.twig",
            array("f" => $form)
        );
    }

    #[Route('/updateBonplan/{id}', name: 'updateBonplanUser')]
    public function updatBonPlan($id, ManagerRegistry $doctrine, Request $request, BonplanRepository $bonplanRep): Response
    {
        $bonplan = $bonplanRep->find($id);
        $form = $this->createForm(BonplanFormType::class, $bonplan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $imagesDirectory = 'C:/xampp/htdocs/images';
                $originalFilename = $imageFile->getClientOriginalName();
                $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);

                try {
                    $imageFile->move($imagesDirectory, $filenameWithoutSpaces);
                } catch (FileException $e) {
                    // Handle exception
                }

                $bonplan->setImage($filenameWithoutSpaces);
            }

            $em->flush();

            return $this->redirectToRoute('all_bonplan');
        }

        return $this->renderForm(
            "bonplan/updateBonplan.html.twig",
            [
                "f" => $form,
                "b" => $bonplan,
            ]
            // array("f" => $form)
        );
    }
    #[Route('/deleteBonplan/{id}', name: 'delete_Bonplan')]
    public function supprimerC($id, BonplanRepository $bonplanRep, ManagerRegistry $doctrine): Response
    {
        // methide de revuperation finAll() find($id) findBy()
        $bonplan = $bonplanRep->find($id);
        // Action supprision
        // methode de persistance : persist() remove() flush()
        $em = $doctrine->getManager();
        $em->remove($bonplan);
        // flush heya el commit
        $em->flush();
        return $this->redirectToRoute('all_bonplan');
    }
//// supprimerjson
    // #[Route('/deleteBonplanJson', name: 'delete_Bonplanjson')]
    // public function supprimerCJson(Request $request,BonplanRepository $bonplanRep, ManagerRegistry $doctrine,NormalizerInterface $Normalizer): Response
    // {
       
    //     // methide de revuperation finAll() find($id) findBy()
    //     $bonplan = $bonplanRep->find( $request->get('id_bonplan'));
    //     // Action supprision
    //     // methode de persistance : persist() remove() flush()
    //     $em = $doctrine->getManager();
    //     $em->remove($bonplan);
    //     // flush heya el commit
    //     $em->flush();
    //     $jsonContent = $Normalizer->normalize($bonplan,'json',['groups'=>'bonplan']);
    //     return new Response("Bonplan Supprimé avec succès".json_encode($jsonContent));
    // }

    /////////////////////////admiiiinnnn/////////////////


    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRep): Response
    {
        $user=$this->getUser();
        return $this->render('admin/indexx.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/admAllBonplan', name: 'admin_all_bonplan')]
    public function adminAllBonplan(BonplanRepository $bonplanRep ,UserRepository $userRep): Response
    {
        $allBonplan = $bonplanRep->orderById();
        $user=$this->getUser();
        return $this->render('admin/allBonplan.html.twig', [
            'allBonplan' => $allBonplan,
            'user' => $user,
        ]);
    }
    // ajouter bonPlan

    #[Route('/admAddBonplan', name: 'admin_add_Bonplan')]
    public function addBonplanA(ManagerRegistry $doctrine, Request $request, UserRepository $userRep): Response
    {
        $user=$this->getUser();
        $admin=$userRep->find(22);
        $bonplan = new Bonplan();
        $form = $this->createForm(BonplanFormType::class, $bonplan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $bonplan->setUser($user);
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $imagesDirectory = 'C:/xampp/htdocs/images';
                $originalFilename = $imageFile->getClientOriginalName();
                $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);

                try {
                    $imageFile->move($imagesDirectory, $filenameWithoutSpaces);
                } catch (FileException $e) {
                    // Handle exception
                }

                $bonplan->setImage($filenameWithoutSpaces);
            }



            $bonplan->setEtat("accepté");
            $em->persist($bonplan);
            $em->flush();
            return $this->redirectToRoute('admin_all_bonplan');
        }

        return $this->renderForm(
            "admin/addBonPlan.html.twig",
            array("f" => $form,'user' => $admin)
        );
    }

    // supprimer bonPlan
    #[Route('/admDeleteBonplan/{id}', name: 'admin_delete_Bonplan')]
    public function supprimerCA($id, BonplanRepository $bonplanRep, ManagerRegistry $doctrine): Response
    {
        // methide de revuperation finAll() find($id) findBy()
        $bonplan = $bonplanRep->find($id);
        
        // Action supprision
        // methode de persistance : persist() remove() flush()
        $em = $doctrine->getManager();
        $em->remove($bonplan);
        // flush heya el commit
        $em->flush();
        return $this->redirectToRoute('admin_all_bonplan');
    }

    // validate bonPlan
    #[Route('/admValidateBonplan/{id}', name: 'admin_validate_Bonplan')]
    public function validateBonplan($id, BonplanRepository $bonplanRep, ManagerRegistry $doctrine): Response
    {
        // methide de revuperation finAll() find($id) findBy()
        $bonplan = $bonplanRep->find($id);
        
        // Action supprision
        // methode de persistance : persist() remove() flush()
        $em = $doctrine->getManager();
        $bonplanRep->validateBonplan($bonplan);
        // flush heya el commit
        $em->flush();
        return $this->redirectToRoute('admin_all_bonplan');
    }
    // ajouter bonPlan

    #[Route('/admUpdateBonplan/{id}', name: 'updateBonplan')]
    public function updatBonPlanA( $id,ManagerRegistry $doctrine, Request $request, BonplanRepository $bonplanRep,UserRepository $userRep): Response
    {
        $bonplan = $bonplanRep->find($id);
        $user=$this->getUser();
        $form = $this->createForm(BonplanFormType::class, $bonplan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $imagesDirectory = 'C:/xampp/htdocs/images';
                $originalFilename = $imageFile->getClientOriginalName();
                $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);

                try {
                    $imageFile->move($imagesDirectory, $filenameWithoutSpaces);
                } catch (FileException $e) {
                    // Handle exception
                }

                $bonplan->setImage($filenameWithoutSpaces);
            }

            $em->flush();

            return $this->redirectToRoute('admin_all_bonplan');
        }

        return $this->renderForm(
            "admin/updateBonplan.html.twig",["f" => $form,
            "b"=>$bonplan,
            'user' => $user,
            ]
            // array("f" => $form)
        );
    }

    //all feedback 

    #[Route('/admAllFeeds', name: 'admin_all_Feeds')]
    public function adminAllFeed(FeedbackRepository $feedRep,UserRepository $userRep): Response
    {
        $allFeeds = $feedRep->orderById();
        $user=$this->getUser();
        return $this->render('admin/allFeeds.html.twig', [
            'allFeeds' => $allFeeds,
            'user' => $user
        ]);
    }

    // supprimer Feedback
    #[Route('/admDeleteFeedback/{id}', name: 'admin_delete_Feed')]
    public function adminDeleteFeedback($id, FeedbackRepository $feedRep, ManagerRegistry $doctrine): Response
    {
        // methide de revuperation finAll() find($id) findBy()
        $feedback = $feedRep->find($id);
        // Action supprision
        // methode de persistance : persist() remove() flush()
        $em = $doctrine->getManager();
        $em->remove($feedback);
        // flush heya el commit
        $em->flush();
        return $this->redirectToRoute('admin_all_Feeds');
    }
    //// supprimerjson
    #[Route('/deleteBonplanJson', name: 'delete_Bonplanjson')]
    public function supprimerCJson(Request $request, BonplanRepository $bonplanRep, ManagerRegistry $doctrine, NormalizerInterface $Normalizer): Response
    {

        // methide de revuperation finAll() find($id) findBy()
        $bonplan = $bonplanRep->find($request->get('id_bonplan'));
        // Action supprision
        // methode de persistance : persist() remove() flush()
        $em = $doctrine->getManager();
        $em->remove($bonplan);
        // flush heya el commit
        $em->flush();
        $jsonContent = $Normalizer->normalize($bonplan, 'json', ['groups' => 'bonplan']);
        return new Response("Bonplan Supprimé avec succès" . json_encode($jsonContent));
    }

    /// afficher detail bonplan Json
    #[Route('/bonplanJson/{id}', name: 'detail_bonplan')]
    public function detailBonPlanJson(ManagerRegistry $doctrine, Request $request, UserRepository $userRep, BonplanRepository $bonplanRep, $id, FeedbackRepository $feedRep, TransportInterface $mailer, NormalizerInterface $Normalizer): Response
    {
        $bonplan = $bonplanRep->find($id);
        $bonplanDetail = $bonplanRep->getBonPlanWithFeedbacks($bonplan);
        $feeds = $feedRep->getFeedbackByBonPlan($bonplanDetail);

        $jsonContent = $Normalizer->normalize([
            'bonplan' => $bonplanDetail,
            'feedbacks' => $feeds
        ], 'json', ['groups' => 'bonplan']);
        return new Response(json_encode($jsonContent));
    }
    //afficher bonplan json
    #[Route('/admAllBonplanJson', name: 'admin_all_bonplanjson')]
    public function adminAllBonplanJson(BonplanRepository $bonplanRep, UserRepository $userRep, NormalizerInterface $Normalizer): Response
    {
        $allBonplan = $bonplanRep->orderById();
        $user = $this->getUser();
        $jsonContent = $Normalizer->normalize($allBonplan, 'json', ['groups' => 'bonplan']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/addFeedback/{id_post}', name: 'app_comment_new_json')]
    public function newaddcommentjson(Request $req, EntityManagerInterface $entityManager, $id_post, NormalizerInterface $Normalizer,BonplanRepository $bonplanRep, TransportInterface $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $feedback = new Feedback();
        
        $comment = $req->get('comment');
        if (!$comment) {
            return new Response('Comment cannot be null or empty', Response::HTTP_BAD_REQUEST);
        }
        $feedback->setCommentaire($comment);
        $feedback->setRate($req->get('rate'));
        if($req->get('report')=="false"){
            $feedback->setReport(false);
        }else{
            $feedback->setReport(true);
        }
        
        //$comment->setIdPost($entityManager->getReference(Post::class, $id_post));
        //$comment->setIdPost($entityManager->getReference(Post::class, (int) $id_post));
        $bonplan=$entityManager->getReference(Bonplan::class, (int) $id_post);

        
        $feedback->setBonplan($bonplan);

        $user = $em->getRepository(User::class)->find($req->get('id_user'));
        $feedback->setUser($user);

        if ($bonplanRep->checkIfAlreadyReported($bonplan, $user) > 0) {
          
            $feedback->setReport(false);
        }
        if ($bonplanRep->checkIfAlreadyRated($bonplan, $user) > 0) {
            
            $feedback->setRate(-1);
        }
        $reportLines = $bonplanRep->count_reports();
        foreach ($reportLines as $line) {
            $bonplanReported = $line[0]->getBonplan();
            $reportCount = $line['report_count'];
            if ($reportCount >= 4) {
                $email = (new Email())
                    ->from('hooligans.pidev@gmail.com')
                    ->to($bonplanReported->getUser()->getEmail())
                    ->subject('Be careful !')
                    ->text("Your bonplan " . $bonplanReported->getNomBonplan() . " has been deleted due to mutliple reports  !");
                //->html('<p>See Twig integration for better HTML integration!</p>');

                $mailer->send($email);
                $em->remove($bonplanReported);
                $em->flush();
                $jsonContent = $Normalizer->normalize("Bonplan deleted with success", 'json', ['groups' => 'comment']);
        return new Response(json_encode($jsonContent));
            }
        }

        $em->persist($feedback);
        $em->flush();
        $jsonContent = [
            'id_comment' => $feedback->getId(),
            'comment' => $feedback->getCommentaire(),
            'rate' => $feedback->getRate(),
            'report' => $feedback->isReport(),
            'user' => [
                'id_user' => $feedback->getUser()->getIdUser(),
            ],
        ];
        $jsonContent = $Normalizer->normalize($jsonContent, 'json', ['groups' => 'comment']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/allFeedsJson/{id_post}', name: 'app_comment_show_json')]
    public function getcomments(Request $req, FeedbackRepository $feedbackRepository, SerializerInterface $serializer, NormalizerInterface $Normalizer, $id_post, BonplanRepository $bonplanRep): Response
    {
        $bonplan = $bonplanRep->find($id_post);
        $bonplanDetail = $bonplanRep->getBonPlanWithFeedbacks($bonplan);
        $feeds = $feedbackRepository->getFeedbackByBonPlan($bonplan);
        //$comments = $feedbackRepository->findBy(['id_post' => $id_post]);
        $jsonContent = [];
        foreach ($feeds as $comment) {
            $jsonContent[] = [
                'id_comment' => $comment->getId(),
                'comment' => $comment->getCommentaire(),
                'rate' => $comment->getRate(),
                'report' => $comment->isReport(),

                'id_user' => $comment->getUser()->getIdUser(),
                'img' => $comment->getUser()->getImg(),
                'nom' => $comment->getUser()->getNom()

            ];
        }
        $jsonContent = $Normalizer->normalize($jsonContent, 'json', ['groups' => 'comment']);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/editFeedJson/{id_comment}', name: 'app_comment_edit_json', methods: ['GET', 'POST'])]
    public function editjsoncomment(Request $req, $id_comment, FeedbackRepository $feedbackRepository, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Feedback::class)->find($id_comment);

        if (!$comment) {
            throw $this->createNotFoundException('Comment not found');
        }

        $comment->setCommentaire($req->get('comment'));
        $comment->setRate($req->get('rate'));
        if($req->get('report')=="false"){
            $comment->setReport(false);
        }else{
            $comment->setReport(true);
        }
        $em->flush();

        $jsonContent = [
            'id_comment' => $comment->getId(),
            'commentaire' => $comment->getCommentaire(),
            'rate' => $comment->getRate(),
            'report' => $comment->isReport(),
        ];

        $jsonContent = $normalizer->normalize($jsonContent, 'json', ['groups' => 'comment']);

        return new Response("Comment updated successfully " . json_encode($jsonContent));
    }

    //DELETE COMMENT JSON
    #[Route('/deleteFeedJson/{id_comment}', name: 'app_comment_delete_json', methods: ['POST'])]
    public function deleteJSONCOMMENT(Request $req, $id_comment, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Feedback::class)->find($id_comment);
        $em->remove($comment);
        $em->flush();
        $jsonContent[] = [
            'id_comment' => $comment->getId(),
            'comment' => $comment->getCommentaire(),
            'rate' => $comment->getRate(),
            'report' => $comment->isReport(),
            'user' => [
                'id_user' => $comment->getUser()->getIdUser(),
            ],
        ];
        $jsonContent = $Normalizer->normalize($jsonContent, 'json', ['groups' => 'comment']);
        return new Response("Comment deleted successfully " . json_encode($jsonContent));
    }
}
