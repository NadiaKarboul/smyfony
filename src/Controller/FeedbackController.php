<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackFormType;
use App\Repository\BonplanRepository;
use App\Repository\FeedbackRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    #[Route('/feedback', name: 'app_feedback')]
    public function index(): Response
    {
        return $this->render('feedback/index.html.twig', [
            'controller_name' => 'FeedbackController',
        ]);
    }
    #[Route('/deleteFeedback/{id}', name: 'delete_feedback')]
    public function deleteFeedback($id, FeedbackRepository $feedRep, ManagerRegistry $doctrine): Response
    {
        // methide de revuperation finAll() find($id) findBy()
        $feedback = $feedRep->find($id);
        // Action supprision
        // methode de persistance : persist() remove() flush()
        $em = $doctrine->getManager();
        $em->remove($feedback);
        // flush heya el commit
        $em->flush();
        return $this->redirectToRoute('detail_bonplan',["id" => $feedback->getBonplan()->getId()]);
    }
    #[Route('/updateFeedback/{id}', name: 'update_feedback')]
    public function updateFeedback($id, FeedbackRepository $feedRep,UserRepository $userRep, Request $request,BonplanRepository $bonplanRep, ManagerRegistry $doctrine): Response
    {
        $feedback = $feedRep->find($id);
        $user=$this->getUser();
        $bonplan = $bonplanRep->getBonPlanWithFeedbacks($feedback->getBonplan()->getId());
        $bp = $bonplanRep->find($feedback->getBonplan()->getId());

        $feeds=$feedRep->getFeedbackByBonPlan($bonplan);
        
        $form = $this->createForm(FeedbackFormType::class, $feedback);
        if($bonplanRep->checkIfAlreadyReported($bonplan,$user)>0){
            $form->add('report', null, [
                'disabled' => true,
            ]);
            $feedback->setReport(false);
        }
        if($feedback->getRate()<0){
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
            

            
            $em->flush();

            return $this->redirectToRoute('detail_bonplan',["id"=>$bp->getId()]);
        }

        return $this->renderForm("bonplan/detailBonplan.html.twig",["f" => $form,
        'bonplan' => $bonplan,
        'feeds'=>$feeds,
        'user'=>$user,
        ]);
    }
    

}
