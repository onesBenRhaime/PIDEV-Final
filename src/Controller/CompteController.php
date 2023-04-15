<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\TypeCompte;
use App\Form\CompteType;
use App\Form\CompteSearchType;
use App\Repository\CompteRepository;
use App\Repository\TypeCompteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\PieChart\PieSlice;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;


#[Route('/compte')]
class CompteController extends AbstractController
{

    #[Route('/statistiques', name: 'app_compte_statistiques', methods: ['POST','GET'])]
    public function statistiques(Request $request ,CompteRepository $compteRepository ,TypeCompteRepository $typeCompteRepository): Response
    {       
            $compte = new Compte();    
            $comptes = []; 
                $comptes = $compteRepository->getStat();
                $prods = array (array("Type compte","Nombre de demandes de COMPTE"));
               $i = 1;
               foreach ($comptes as $prod){
                   $prods[$i] = array($prod["type"],$prod["nbre"]);
                   $i= $i + 1;
               }   
               $bar = new Barchart();
               $pieChart = new Piechart();
               $bar->getData()->setArrayToDataTable($prods);
               $pieChart->getData()->setArrayToDataTable($prods);

               $bar->getOptions()->setTitle('Statistique de nombre de Demandes de compte par Types');
               $bar->getOptions()->getHAxis()->setTitle('Statistique de nombre de Demandes de compte par Types');
               $bar->getOptions() ->getTitleTextStyle()->setColor('#CF0000');
               $bar->getOptions()->getHAxis()->setMinValue(0);
               $bar->getOptions()->setWidth(900);
               $bar->getOptions()->setHeight(500);
   
               $pieChart->getOptions()->setTitle('Statistique de nombre de Demandes par Types');
               $pieChart->getOptions()->setHeight(400);
               $pieChart->getOptions()->setWidth(400);
               $pieChart->getOptions() ->getTitleTextStyle()->setColor('#CF0000');
               $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);
                
           return $this->render('compte/comptes_chart.html.twig', [
               'bar' => $bar,
               'pieChart' => $pieChart,
   
           ]);
    }
    
    #[Route('/search', name: 'ajax_search', methods: ['GET'])]
    public function searchAction(Request $request, CompteRepository $charityDemandRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        $requestString = $request->get('q');

        $charitydemands = $em->getRepository('App\Entity\Compte')->findEntitiesByString($requestString);

        if (!$charitydemands) {
            $result['charity_demands']['error'] = "NOT FOUND";
        } else {
            $result['charity_demands'] = $this->getRealEntities($charitydemands);
        }

        return new Response(json_encode($result));
    }

    public function getRealEntities($charitydemands)
    {

        foreach ($charitydemands as $charitydemand) {
            $realEntities[$charitydemand->getId()] = $charitydemand->getStatue();
        }
        return $realEntities;
    }
    #[Route('/view', name: 'app_charity_demand_View', methods: ['GET', 'POST'])]
    public function View(CompteRepository $charityDemandRepository,  Request $request): Response
    {
        $charitydemands = $charityDemandRepository->findAll();
        $form = $this->createForm(CompteSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchquery = $form->getData()['statue'];
            $searchquery = $form->getData()['rib'];
            $charitydemands = $charityDemandRepository->search($searchquery);
        }
        return $this->render('compte/search.html.twig', [
            'charity_demands' => $charitydemands,
            'form' => $form->createView()
        ]);
    }

    // #[Route('/view', name: 'app_charity_demand_View', methods: ['GET', 'POST'])]
    // public function View(CompteRepository $charityDemandRepository,  Request $request): Response
    // {
    //     $charitydemands = $charityDemandRepository->findAll();
    //     $form = $this->createForm(DemandSearchType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $searchquery = $form->getData()['statue'];
    //         $charitydemands = $charityDemandRepository->search($searchquery);
    //     }
    //     return $this->render('compte/accountDeposit_Ad.html.twig', [
    //         'comptes' => $charitydemands,
    //         'form' => $form->createView()
    //     ]);
    // }
    
   /****USER****/   

    #[Route('/createAccount', name: 'compte_create', methods: ['GET', 'POST'])]
    public function new(Request $request, CompteRepository $compteRepository,SluggerInterface $slugger): Response
    {
        $compte = new Compte();        
        $dateCreation= new \DateTime('now');
        $dateFermeture=new \DateTime('now');
        $compte->setDateCreation($dateCreation);
        $compte->setDateFermeture($dateFermeture);
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image1 = $form->get('cinS1')->getData();
            $image2 = $form->get('cinS2')->getData();
            $image3 = $form->get('otherDoc')->getData();
            if ($image1) {
                $originalFilename = pathinfo($image1->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$image1->guessExtension();
                try {
                    $image1->move(
                        $this->getParameter('brochures_directory1'),
                        $newFilename
                    );
                } catch (FileException $e) {
                
                }
              $compte->setCinS1($newFilename);
            }            
            if ($image2) {
                $originalFilename = pathinfo($image2->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$image2->guessExtension();
                try {
                    $image2->move(
                        $this->getParameter('brochures_directory1'),
                        $newFilename
                    );
                } catch (FileException $e) {
                
                }
              $compte->setCinS2($newFilename);
            }
            if ($image3) {
                $originalFilename = pathinfo($image3->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$image3->guessExtension();
                try {
                    $image3->move(
                        $this->getParameter('brochures_directory1'),
                        $newFilename
                    );
                } catch (FileException $e) {
                
                }
              $compte->setOtherDoc($newFilename);
             
            }
            $compte->setRib("in progress...");   
            $compte->setSolde("in progress...");
            $compte->setStatue("in progress");
            $compteRepository->save($compte, true);

            return $this->redirectToRoute('All_comptes', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('compte/create.html.twig', [
            'compte' => $compte,   
            'form' => $form,
        ]);
    }

    #[Route('/allCompteFiltre', name: 'allcompteFiltre')]
    public function list(Request $request, CompteRepository $compteRepository)
    {
        $type = $request->query->get('type');
        
        if ($type == 'all') {
            $comptes = $compteRepository->findAll();
        } else {
            $comptes = $compteRepository->findByType($type);
        }
    
        $typecompte = $this->getDoctrine()
            ->getRepository(TypeCompte::class)
            ->findAll();
    
        return $this->render('compte/index.html.twig', [
            'comptes' => $comptes,
            'typecompte' => $typecompte
        ]);
    }
    
    #[Route('/compte_all', name: 'All_comptes')]
    public function index(CompteRepository $compteRepository, TypeCompteRepository $typeRepository ): Response
    {
        $typecompte= $typeRepository->findAll();
        return $this->render('compte/index.html.twig', [
            'comptes' => $compteRepository->findAll(),
            'typecompte' => $typecompte,
        ]);
    }

    #[Route('/details/{id}', name: 'compte_details', methods: ['GET'])]
    public function show(Compte $compte): Response
    {
        return $this->render('compte/details.html.twig', [
            'compte' => $compte,
        ]);
    }
    

    #[Route('/delete/{id}', name: 'app_compte_delete', methods: ['POST'])]
    public function deleteDU(Request $request, Compte $compte, CompteRepository $compteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compte->getId(), $request->request->get('_token'))) {
            $compteRepository->remove($compte, true);
        }

        return $this->redirectToRoute('All_comptes', [], Response::HTTP_SEE_OTHER);
    }
    /***********Admin*******/ 


    #[Route('/account_Deposit', name: 'all_deposits')]
    public function requestAccount(CompteRepository $compteRepository): Response
    {
        return $this->render('compte/accountDeposit_Ad.html.twig', [
            'comptes' => $compteRepository->findAll(),
        ]);
    }
    
    #[Route('/deposit/{id}', name: 'compte_Admin_Show', methods: ['GET', 'POST'])]
    public function Comptes(Request $request, Compte $compte, CompteRepository $compteRepository): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compteRepository->save($compte, true);

            return $this->redirectToRoute('all_deposits', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('compte/edit_Ad.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/accept/{id}', name: 'app_compte_accept', methods: ['GET','POST'])]
    public function acceptAccount(Request $request, Compte $compte, CompteRepository $compteRepository): Response
    {
        if ($this->isCsrfTokenValid('accept'.$compte->getId(), $request->request->get('_token'))) {
            $compte->setStatue('valide');   
            $compte->setSolde('0.0');                  
            $randomInt = random_int(0, 99999999999999);
            $randomString = str_pad($randomInt, 14, '0');
            $compte->setRib($randomString);
        //send mail accept 


            $compteRepository->save($compte, true);
        }
        return $this->redirectToRoute('all_deposits', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reject/{id}', name: 'app_compte_reject', methods: ['POST'])]
    public function rejectAccount(Request $request, Compte $compte, CompteRepository $compteRepository): Response
    {
        if ($this->isCsrfTokenValid('reject'.$compte->getId(), $request->request->get('_token'))) {
            $compte->setStatue('rejected');          
            $dateFermeture=new \DateTime('now');
            $compte->setDateFermeture($dateFermeture);
            $compteRepository->save($compte, true);
        }

        return $this->redirectToRoute('all_deposits', [], Response::HTTP_SEE_OTHER);
    }
   


}
