<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository ;
use App\Entity\Produit;
use App\Repository\CategorieRepository ;
use App\Entity\Categorie;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry ;
use App\Form\CategoryFormType ;
use App\Form\ProduitFormType ;
use CMEN\GoogleChartsBundle\CMENGoogleChartsBundle\Barchart ;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart ;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart ;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BareChart ;
use CMEN\GoogleChartsBundle\GoogleCharts;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Garagec;
use App\Repository\GaragecRepository;
use App\Repository\MaintenanceRepository;
use App\Repository\BonplanRepository;
use App\Entity\Maintenance;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index1(): Response
    {
        return $this->render('homeuser.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }

                 
    //Afficher Produit
    #[Route('/userafficheproduit', name: 'affichep')]
    public function affichep(ProduitRepository $Rep,CategorieRepository $Rep1,PaginatorInterface $paginator , Request $request,): Response
    { 
        $user=$this->getUser();
        $Categorie=$Rep1->findAll();
        //on recupere le filtre
        $filters= $request->get("categories");
        
       //Je recupere el produit belfonction eli aamaltha 
        $Produit=$Rep->findByCategoryId($filters);
        $Produit = $paginator->paginate(
            $Produit, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,

            8/*limit per page*/,
           
        );
       
        $pageNumber = $Produit->getCurrentPageNumber(); 
        // On vérifie si on a une requête Ajax
        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView('produit/content.html.twig', [
                    'p'=>$Produit  ,
                    'c'=>$Categorie  ,
                    'pagenum'=>$pageNumber ,
                    'user'=>$user
            
                    ])
            ]);
        }
      
        return $this->render('produit/afficherProduitUser.html.twig', [
        'p'=>$Produit  ,
        'c'=>$Categorie  ,
        'pagenum'=>$pageNumber ,
        

        ]);
    }

 
     //Fonction qui retourne les produits r sous format json 
    #[Route('/userafficheproduitJSON', name: 'app_AfficheproduitJSON1')]
    public function afficheProduitjson1(ProduitRepository $Rep,CategorieRepository $Rep1,NormalizerInterface $Normalizer): Response
    { $Produit=$Rep->findAll();
    
        $jsonContent = $Normalizer->normalize($Produit,'json',['groups'=>['produit','categorie']]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/afficheproduitJson2', name: 'app_AfficheproduitJSON2')]
public function afficheProduitjson2(ProduitRepository $Rep,CategorieRepository $Rep1,NormalizerInterface $Normalizer): Response
{
    


    // Create a DTO to store the normalized data for each lignePanier with associated Produit
    $lignesPanierProduits = [];
    foreach ($Produit as $Produit) {
        // Get the associated Produit for the given lignePanier
        $Categorie = $CategorieRepository->find($Produit->getId());
    
        // Normalize the Produit object
        $normalizedProduit = $Normalizer->normalize($produit, 'json', ['groups' => ['produits', 'categ']]);
    
        // Add the normalized "produit" object to the final array
        $lignesPanierProduits[] = $normalizedProduit;
    }

    // Return the normalized DTO as a JSON response
    return new Response(json_encode($lignesPanierProduits));
}




    //Afficherdetail
    #[Route('/useraffichedetailproduitN/{id}', name: 'affichedetailN')]
    public function affichedetail(ProduitRepository $Rep, int $id ): Response
    { $Produit=$Rep->find($id);
     $Produits=$Rep->findAll();
        
        return $this->render('produit/afficherDetailProduitUser.html.twig', [
        'p'=>$Produit  ,
        's'=>$Produits  ,
        
        

        ]);
}

  //Afficherdetail JSON
  #[Route('/useraffichedetailproduitNJson/{id}', name: 'affichedetailNJSON')]
  public function affichedetailJSON(ProduitRepository $Rep, int $id ,NormalizerInterface $Normalizer): Response
  { $Produit=$Rep->find($id);
   
      
   $jsonContent = $Normalizer->normalize($Produit,'json',['groups'=>'produit']);
   return new Response(json_encode($jsonContent));
      
      

    
}

//////////////admin//////////
#[Route('/adm', name: 'app_admin')]
public function index(CategorieRepository $Rep,BonplanRepository $bonplanRep, ProduitRepository $Rep1,GaragecRepository $r,MaintenanceRepository $r1): Response
{  $Categorie=$Rep->findAll();
 $Produit=$Rep1->findAll();


 $garageC=$r->orderById();
 $maintenance=$r1->orderById();

//    $chart = new \CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart();
//     $chart->getData()->setArrayToDataTable([
//         ['Year', 'Sales', 'Expenses'],
//         ['2014', 1000, 400],
//         ['2015', 1170, 460],
//         ['2016', 660, 1120],
//         ['2017', 1030, 540]
//     ]);

// Récupérer les données de votre base de données
$em = $this->getDoctrine()->getManager();
$query = $em->createQuery('SELECT p.nom_prod, p.quantite_prod FROM App\Entity\Produit p');
$results = $query->getResult();

// Convertir les données en un format de données pris en charge par PieChart
$data = [['Product', 'Quantity']];
foreach ($results as $result) {
$data[] = [$result['nom_prod'], (int) $result['quantite_prod']];
}

    $pieChart = new PieChart();
    $pieChart->getData()->setArrayToDataTable($data);
    $pieChart->getOptions()->setTitle(' Quantité en stock');
    $pieChart->getOptions()->setHeight(400);
    $pieChart->getOptions()->setWidth(700);
    $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
    $pieChart->getOptions()->getTitleTextStyle()->setColor('#bde0ff');
    $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
    $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
    $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);
    $pieChart->getOptions()->setColors(['#bde0ff', '#c8f4d5', '#e3f1cb', '#d4e9da', '#d7ffe4', '#dde3e3','#d7e5db','#afeeee','#e6f6c6','#fdf1b8']);
    $pieChart->getOptions()->setIs3D(true);
    $pieChart->getOptions()->getLegend()->getTextStyle()->setFontName('Arial');
    $pieChart->getOptions()->getLegend()->getTextStyle()->setFontSize(14);
    $pieChart->getOptions()->getLegend()->getTextStyle()->setColor('#666666');
    $pieChart->getOptions()->getPieSliceTextStyle()->setFontName('Arial');
    $pieChart->getOptions()->getPieSliceTextStyle()->setFontSize(14);
    $pieChart->getOptions()->setBackgroundColor('#f5f5f5');
    $pieChart->getOptions()->getLegend()->setPosition('left');
    $pieChart->getOptions()->setPieSliceText('label');

///////2EME CHART///////
 $query1 = $em->createQuery('SELECT p.nom_prod, p.prix_prod FROM App\Entity\Produit p');
$result1 = $query1->getResult();

// Convertir les données en un format de données pris en charge par PieChart
$data1 = [['Product', 'Price']];
foreach ($result1 as $result) {
$data1[] = [$result['nom_prod'], (float) $result['prix_prod']];}

$line = new LineChart();
$line->getData()->setArrayToDataTable($data1);
$line->getOptions()->setTitle('Variation des prix des produits');
$line->getOptions()->setCurveType('function');
$line->getOptions()->setLineWidth(4);
$line->getOptions()->getLegend()->setPosition('none');
$line->getOptions()->setHeight(400);
$line->getOptions()->setWidth(700);
$line->getOptions()->setPointShape('point');
$line->getOptions()->getTitleTextStyle()->setBold(true);
$line->getOptions()->getTitleTextStyle()->setColor('#bde0ff');
$line->getOptions()->setBackgroundColor('#f5f5f5');
$line->getOptions()->getHAxis()->setTitle('Produits');
$line->getOptions()->getVAxis()->setTitle('Price');
$line->getOptions()->getTooltip()->setIsHtml(true);
$line->getOptions()->setColors(['#bde0ff', '#DC3912', '#FF9900']);
$line->getOptions()->getLegend()->setPosition('top');
$line->getOptions()->getAnimation()->setDuration(1000);
$line->getOptions()->setPointSize(7);


//////3EME CHART/////
$query2 = $em->createQuery('SELECT c.nom_categorie, SUM(p.quantite_prod) as total_quantite FROM App\Entity\Produit p JOIN p.categorie c GROUP BY p.categorie');
$result2 = $query2->getResult();

// Convertir les données en un format de données pris en charge par BarChart
$data2 = [['Categorie', 'Nbre produit']];
foreach ($result2 as $result) {
$data2[] = [$result['nom_categorie'], (int) $result['total_quantite']];
}

$bar = new \CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart();


$bar->getData()->setArrayToDataTable($data2);
$bar = new \CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart();

$bar->getData()->setArrayToDataTable($data2);
$bar->getOptions()
->setTitle('Quantité de produits par catégorie')
->setColors(['#4AA3A2'])// Change the colors here
->setBackgroundColor('#f5f5f5')
->setHeight(400)
->setWidth(600)
->getLegend()
  ->setPosition('none');
 
////4eme chart
$query3 = $em->createQuery('SELECT c.type_categorie, SUM(p.prix_prod) as total_quantite FROM App\Entity\Produit p JOIN p.categorie c GROUP BY c.type_categorie');
$results3 = $query3->getResult();

// Convertir les données en un format de données pris en charge par PieChart
$data3 = [['Product', 'Quantity']];
foreach ($results3 as $result) {
$data3[] = [$result['type_categorie'], (float) $result['total_quantite']];
}

    $pieChart1 = new PieChart();
    $pieChart1->getData()->setArrayToDataTable($data3);
    $pieChart1->getOptions()->setTitle('');
    $pieChart1->getOptions()->setHeight(400);
    $pieChart1->getOptions()->setWidth(700);
    $pieChart1->getOptions()->getTitleTextStyle()->setBold(true);
    $pieChart1->getOptions()->getTitleTextStyle()->setColor('#bde0ff');
    $pieChart1->getOptions()->getTitleTextStyle()->setItalic(true);
    $pieChart1->getOptions()->getTitleTextStyle()->setFontName('Arial');
    $pieChart1->getOptions()->getTitleTextStyle()->setFontSize(20);
    $pieChart1->getOptions()->setColors(['#bde0ff', '#c8f4d5', '#e3f1cb', '#d4e9da', '#d7ffe4', '#dde3e3','#d7e5db','#afeeee','#e6f6c6','#fdf1b8']);
    $pieChart1->getOptions()->setIs3D(true);
    $pieChart1->getOptions()->getLegend()->getTextStyle()->setFontName('Arial');
    $pieChart1->getOptions()->getLegend()->getTextStyle()->setFontSize(14);
    $pieChart1->getOptions()->getLegend()->getTextStyle()->setColor('#666666');
    $pieChart1->getOptions()->getPieSliceTextStyle()->setFontName('Arial');
    $pieChart1->getOptions()->getPieSliceTextStyle()->setFontSize(14);
    $pieChart1->getOptions()->setBackgroundColor('#f5f5f5');
    $pieChart1->getOptions()->getLegend()->setPosition('left');
    $pieChart1->getOptions()->setPieSliceText('label');
    $pieChart1->getOptions()->setPieSliceText('value');


    $allBonplan = $bonplanRep->getRecentWithFeedbacks();

    return $this->render('homeadmin.html.twig', [
        'controller_name' => 'AdminController',
        'c'=>$Categorie ,
        'p'=>$Produit ,
       // 'charts'=>$chart ,
        'pieCharts'=>$pieChart ,
        'lines'=>$line,
        'bars'=>$bar,
        'pieCharts1'=>$pieChart1,
        'g'=>$garageC, 
        'm'=> $maintenance,
        'allBonplan'=>$allBonplan,
    ]);
}




//Ajouter categorie

    #[Route('/admaddcategory', name: 'addcategory')]
    public function addCategorie(ManagerRegistry $doctrine,Request $request)
            {
            $Categorie= new Categorie();
            $form=$this->createForm(CategoryFormType::class,$Categorie);
                $form->handleRequest($request);
                if($form->isSubmitted()){
                    $em =$doctrine->getManager() ;
                    $em->persist($Categorie);
                    $em->flush();
                    return $this->redirectToRoute("affichecategorie");}
            return $this->renderForm("Categorie/addCategoryAdmin.html.twig",
                    array("f"=>$form));
                }
                
//Afficher Categorie
    #[Route('/admaffichecategorie', name: 'affichecategorie')]
    public function afficheCategorie(CategorieRepository $Rep): Response
    { $Categorie=$Rep->orderById();
    
        return $this->render('Categorie/afficherCategorieAdmin.html.twig', [
        's'=>$Categorie  ,
        

        ]);
    }
  
//Afficher Categorie Json
#[Route('/admaffichecategorieJson', name: 'affichecategorie')]
public function afficheCategorieJson(CategorieRepository $Rep,NormalizerInterface $Normalizer): Response
{ $Categorie=$Rep->orderById();

    $jsonContent = $Normalizer->normalize($Categorie,'json',['groups'=>'categorie']);
            return new Response(json_encode($jsonContent));


}


//Supprimer Categorie
#[Route('/admsupprimecategorie/{id}', name: 'supprimec')]
public function supprimec($id,CategorieRepository $r, ManagerRegistry $doctrine): Response
{   //recuperer la categorie a supprimer
    $Categorie=$r->find($id);
    //action supprmer
    $em=$doctrine->getManager();
    $em->remove($Categorie);
    $em->flush();
    return $this->redirectToRoute('affichecategorie',); 
}

//Update Categorie

#[Route('/admupdatecategorie/{id}', name: 'updatec')]
public function modifierCategorie(ManagerRegistry $doctrine,Request $request,$id,CategorieRepository $r)
                       {
      { //récupérer la categorieà modifier
        $Categorie=$r->find($id);
    $form=$this->createForm(CategoryFormType::class,$Categorie);
     $form->handleRequest($request);
     if($form->isSubmitted()){
    $em =$doctrine->getManager() ;
         $em->flush();
        return $this->redirectToRoute("affichecategorie");}
         return $this->renderForm("Categorie/addCategoryAdmin.html.twig",
            array("f"=>$form));
                        }
                    }

                ////////////////////

         //Afficher Produit
    #[Route('/admafficheproduit', name: 'afficheproduit')]
    public function afficheProduit(ProduitRepository $Rep): Response
    { $Produit=$Rep->orderById();
    
        return $this->render('produit/afficherProduitAdmin.html.twig', [
        's'=>$Produit  ,
        

        ]);
    }   

    //Fonction qui retourne les produits r sous format json 
    #[Route('/admafficheproduitJSON', name: 'app_AfficheproduitJSON')]
    public function afficheProduitjson(ProduitRepository $Rep,NormalizerInterface $Normalizer): Response
    { $Produit=$Rep->orderById();
    
        $jsonContent = $Normalizer->normalize($Produit,'json',['groups'=>'produit']);
        return new Response(json_encode($jsonContent));
    }

    //Ajouter produit

    #[Route('/admaddproduit', name: 'addproduit')]
    public function addProduit(ManagerRegistry $doctrine,Request $request, TransportInterface $mailer)
            {
            $Produit= new Produit();
            $form=$this->createForm(ProduitFormType::class,$Produit);
                $form->handleRequest($request);
                if($form->isSubmitted()&& $form->isValid()){
                    $em =$doctrine->getManager() ;
                    $imageFile = $form->get('image')->getData();

            // Vérifier si le produit existe déjà en base de données
           $existingProduit = $em->getRepository(Produit::class)->findOneBy([
         'nom_prod' => $Produit->getNomProd(),
          
              ]);
              $nomprod = $form->get('nom_prod')->getData();
              $quantprod = $form->get('quantite_prod')->getData();
              if ($existingProduit) {
                // Si le produit existe déjà, mettre à jour la quantité
                $existingProduit->setQuantiteProd($existingProduit->getQuantiteProd() + $Produit->getQuantiteProd());
                $this->addFlash('error1', 'La quantité du produit '. $nomprod .'  est modifiée avec succes.');
            } else {
                // Sinon, ajouter le nouveau produit à la base de données 


                    if ($imageFile) {
                        $imagesDirectory = 'C:/xampp/htdocs/images';
                        $originalFilename = $imageFile->getClientOriginalName();
                        $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);
        
                        try {
                            
                            $imageFile->move($imagesDirectory,$filenameWithoutSpaces);
                            
                        } catch (FileException $e) {
                            // Handle exception
                        }
                        $Produit->setImage($filenameWithoutSpaces);
                    }
                    
        
                 
                    $em->persist($Produit);
                   
                    $this->addFlash('error', 'Le produit '. $nomprod .' est ajoutée avec succes.');
                    $users = $this->getDoctrine()->getRepository(User::class)->findAll();
                    foreach($users as $user){
                     $mail = $user->getEmail(); 
                     $contenu = $this->renderView('produit/mailProduit.html.twig', [
                        'p'=>$Produit  ,
                          ]);
                        $email = (new Email())
                         ->from('helamoalla91@gmail.com')
                         ->to($mail)
                         ->subject('Un nouveau Produit Ajouté avec succes !')
                         ->html($contenu);
                     //// Envoyer l'email
                      $mailer->send($email);
                 }             
                //     // Créer l'email
                 
            }
                    $em->flush();

  
                  
                    return $this->redirectToRoute('afficheproduit');}
        
                   return $this->renderForm("produit/addProduitAdmin.html.twig",
                   
                    array("f"=>$form),);
                }
    
             
           ///ajout produit json
           
           
           

//Supprimer Produit
#[Route('/admsupprimeproduit/{id}', name: 'supprimep')]
public function supprimep($id,ProduitRepository $r, ManagerRegistry $doctrine): Response
{   //recuperer le produit a supprimer
   $Produit=$r->find($id);
   //action supprimer
   $em=$doctrine->getManager();
   $em->remove($Produit);
   $em->flush();
   return $this->redirectToRoute('afficheproduit',); 
}
 

//UpdateProduit

#[Route('/admupdateproduit/{id}', name: 'updatep')]
public function modifierProduit(ManagerRegistry $doctrine,Request $request,$id,ProduitRepository $r)
                      {
     { //récupérer le produit a modifier
       $Produit=$r->find($id);
   $form=$this->createForm(ProduitFormType::class,$Produit);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
   $em =$doctrine->getManager() ;
   $imageFile = $form->get('image')->getData();
      
   if ($imageFile) {
       $imagesDirectory = 'C:/xampp/htdocs/images';
       $originalFilename = $imageFile->getClientOriginalName();
       $filenameWithoutSpaces = str_replace(' ', '_', $originalFilename);

       try {
           
           $imageFile->move($imagesDirectory,$filenameWithoutSpaces);
           
       } catch (FileException $e) {
           // Handle exception
       }
       $Produit->setImage($filenameWithoutSpaces);
   }
   

   //$bonplan->setImage("img.jpg");

   $em->persist($Produit);
   $em->flush();
   return $this->redirectToRoute('afficheproduit');}

  return $this->renderForm("produit/addProduitAdmin.html.twig",
   array("f"=>$form));
}}



   //Afficherdetail
   #[Route('/admaffichedetailproduit/{id}', name: 'affichedetailp')]
   public function affichedetailp(ProduitRepository $Rep, int $id ): Response
   { $Produit=$Rep->find($id);
   
       
       return $this->render('produit/afficherDetailProduitAdmin.html.twig', [
       'p'=>$Produit  ,


       ]);
}

}