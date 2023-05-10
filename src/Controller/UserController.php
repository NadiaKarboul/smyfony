<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Formation;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\Panier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;






#[Route('/user')]
class UserController extends AbstractController {

    
    #[Route('/signeIn', name: 'app_signeIn')]
    public function signeIn(Request $request,UserPasswordHasherInterface $passwordHasher  ,EntityManagerInterface $entityManager,SerializerInterface $serializer   ) {
        $email = $request->get('email');
        $mdp = $request->get('mdp');
       
        $user = $entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
        if (isset($user)) {
            $isValid = $passwordHasher->isPasswordValid($user,$mdp);
            if ($isValid) {
               
                $userJSON = $serializer->serialize($user,'json');
                return new Response($userJSON);
            }
            else return new Response('incorrect password');

        }
        else return new Response('user not found');
        }
      

#[Route('/', name: 'app_user_index', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $userEntity = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['idUser' => $user->getIdUser()]);

    return $this->render('user/index.html.twig', [
        'user' => $userEntity,
    ]);
}

    #[Route('/search',name: 'app_user_searchusers',methods:['GET'])]
    public function searchUsers(Request $req,NormalizerInterface $normalizer,EntityManagerInterface $entityManager)
    {

        $searchValue = $req->get('searchValue');
        $users = $entityManager->getRepository(User::class)->findUsersByName($searchValue);
        $jsonContent = $normalizer->normalize($users,'json');
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

    #[Route('/{idUser}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
 



   





    #[Route('/{idUser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{idUser}/test', name: 'test', methods: ['GET'])]
    public function show1(): Response
    {
        $user = $this->getUser();
     
    
        return $this->render('user/test.html.twig', [
            'user' => $user,
        ]);


        
    }







    
    #[Route('/registerjson/new', name: 'app_registerjson')]
    public function registerjson(Request $request,EntityManagerInterface $em, NormalizerInterface $Normalizer, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $panier= new Panier();
        $userRole = $em->getRepository(Role::class)->find(2);
        $user->setIdRole($userRole);
        $user->setEtatUser(0);
        $cryptedpassword = $userPasswordHasher->hashPassword($user , $request->get('mdp'));
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom'));
        $user->setNumTel($request->get('numTel'));
        $user->setEmail($request->get('email'));
        $user->setMdp($cryptedpassword);
        $user->setAdresse($request->get('adresse'));
        $user->setAge($request->get('age'));
     
         
        $em->persist($user);
        $em->flush();

            
        

        $user->setImg("hela.jpg");
        $em->persist($user);
        $em->flush();

        $panier->setUser($user);
        $panier->setId($user->getIdUser());
        $em->persist($panier);
        $em->flush();
        // Return JSON response
            $jsonContent = $Normalizer->normalize($user,'json',['groups'=>'user']);
            return new Response(json_encode($jsonContent));
        }


       
        
        }







