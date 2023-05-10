<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Formation;
use App\Entity\User;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;

class AdminController extends AbstractController
{



    #[Route('/admin/modify/{id}', name: 'app_admin_modify')]
    public function modify(Request $request, $id,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $selectedOption = $request->request->get('my_select');

        $role = $entityManager->getRepository(Role::class)->findOneBy(['idRole' => $selectedOption]);

       $user->setIdRole($role);
       $entityManager->flush();
       return $this->redirectToRoute("app_admin_gererusers",[]);

   
        
    }


    #[Route('/admin/user/delete/{idUser}', name: 'app_user_delete')]
    public function deleteUser(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_gererusers');
    }
    #[Route('/admin/formation',name: 'app_admin_gererformation')]
    public function gererFormation(EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $formations = $em->getRepository(Formation::class)->findAll();
        return $this->render('admin/FormationBack.html.twig',[
            'formations' => $formations
        ]);

    }




    #[Route('/admin/user/delete/{idUser}', name: 'app_user_deletejson')]
    public function deleteUserjson(User $user ,NormalizerInterface $Normalizer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $jsonContent = $Normalizer->normalize($user,'json',['groups'=>'user']);
        return new Response("user Supprimé avec succès".json_encode($jsonContent));
    }
    
    




    #[Route('/admin/users_list', name: 'app_admin_gererusers')]
    public function gererUsers(EntityManagerInterface $entityManager, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $entityManager->getRepository(User::class)->findAll();
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('idRole', ChoiceType::class, [
                'choices' => [
                    'Admin' => 1,
                    'User' => 2,
                    'Recruteur' => 3
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Role'
            ])
            ->add('Modifier', SubmitType::class)
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIdUser($form->get("idRole")->getData());
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_gererusers');
        }
    
        return $this->render('admin/usersBack.html.twig', [
            'form' => $form->createView(), // pass the form view instead of the form itself
            'users' => $users,
            'user'=>$user
        ]);
    }





    #[Route('/admin/users_listjson', name: 'app_admin_gererusersjson')]
    public function gererUsersjson(EntityManagerInterface $entityManager, Request $request , NormalizerInterface $Normalizer)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $entityManager->getRepository(User::class)->findAll();
        
        $form = $this->createFormBuilder($users)
            ->add('idRole', ChoiceType::class, [
                'choices' => [
                    'Admin' => 1,
                    'User' => 2,
                    'Recruteur' => 3
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Role'
            ])
            ->add('Modifier', SubmitType::class)
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $users->setIdUser($form->get("idRole")->getData());
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_gererusersjson');
        }
    
        $jsonContent = $Normalizer->normalize($users,'json',['groups'=>'user']);
        return new Response(json_encode($jsonContent));
    }






    /////////////////naadiiiaaaaa////////////////
    #[Route('/payement', name: 'payement')]
 
   
    // ...
    
    public function processPayment(Request $request,ManagerRegistry $doctrine)
    {
        // Configurer la clé secrète Stripe
        Stripe::setApiKey('sk_test_51MidOiDi4uLa1UR4QzBVLHTMxTOlUHa9RnQRtqkFmwxWciJlPRdoI6BWkRj0C9wXfXSCanlbW3vGha3JIp08N2kc00EvGQ49ci');
    
        try {
            // Créer une charge avec le token de paiement
            $charge = Charge::create([
                'amount' => $request->request->get('amount'),
 
                'currency' => 'USD',
                'source' => $request->request->get('stripeToken'),
            ]);
    $user=$this->getUser();
    $user->setQuota($request->request->get('amount'));
    $em =$doctrine->getManager() ;
    $em->flush();
            // La charge a été réussie
            return $this->redirectToRoute('app_home');
        } catch (\Stripe\Exception\CardException $e) {
            // La carte a été refusée
            $errorMessage = $e->getError()->message;
            dump($errorMessage);
            return $this->redirectToRoute('payement1', ['error' => $errorMessage]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // La requête était malformée
            $errorMessage = $e->getError()->message;
            dump($errorMessage);
            return $this->redirectToRoute('payement1', ['error' => $errorMessage]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Une erreur s'est produite avec Stripe
            $errorMessage = $e->getError()->message;
            dump($errorMessage);
            return $this->redirectToRoute('payement1', ['error' => $errorMessage]);
        }
    }
    #[Route('/payement1', name: 'payement1')]
   public function processPayment1(Request $request){
    return $this->Renderform('payement.html.twig');
   }
}    
