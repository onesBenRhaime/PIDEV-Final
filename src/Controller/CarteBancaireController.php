<?php

namespace App\Controller;

use App\Entity\CarteBancaire;
use App\Form\CarteBancaireType;
use App\Entity\TypeCarte;
use App\Repository\CarteBancaireRepository;
use App\Repository\TypeCarteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\SearchbyType;


#[Route('/carte/bancaire')]
class CarteBancaireController extends AbstractController
{
    #[Route('/allStatistique', name: 'statistique', methods: ['GET'])]
    public function Stat( CarteBancaireRepository $CarteBancaireRepository  , SerializerInterface $serialiser)
     {
      $CarteBancaire = $CarteBancaireRepository->findAll();
      $CarteBancaireCount = count($CarteBancaire);
      return new Response($CarteBancaireCount);       
    }
    
    #[Route('/appcartebancaire', name: 'appcartebancaire')]
    public function list(Request $request,CarteBancaireRepository $carteBancaireRepository)
    {
        $nom = $request->query->get('type');

        if ($nom=="all"){
            $carte_bancaires=  $carteBancaireRepository->findAll();
        }else{
            $carte_bancaires=$carteBancaireRepository->findByType($nom);
        }
        $Typecarte = $this->getDoctrine()
        ->getRepository(TypeCarte::class)
        ->findAll();
        return $this->render('carte_bancaire/index.html.twig', [
            'carte_bancaires'=> $carte_bancaires,'Typecarte'=>$Typecarte
        ]);
    }
    #[Route('/filtre/{id}', name: 'app_carte_bancaire', methods: ['GET'])]
    public function index2(CarteBancaireRepository $carteBancaireRepository, Request $request,$nom): Response
    { 
      $cartes = $carteBancaireRepository->findByType($nom);
      return $this->render('carte_bancaire/index.html.twig', [
        'carte_bancaires' => $cartes,
        'Typecarte' => $Typecarte,
        'type' => $type,
      ]);
    }
   
   
    #[Route('/', name: 'app_carte_bancaire_index', methods: ['GET'])]
    public function index(CarteBancaireRepository $carteBancaireRepository , TypeCarteRepository $typeCarteRepository): Response
    {  
        $Typecarte= $typeCarteRepository->findAll();        
        return $this->render('carte_bancaire/index.html.twig', [
            'carte_bancaires' => $carteBancaireRepository->findAll(),
            'Typecarte' =>$Typecarte
        ]);
    }

    #[Route('/new', name: 'app_carte_bancaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CarteBancaireRepository $carteBancaireRepository,SluggerInterface $slugger): Response
    {
        $carteBancaire = new CarteBancaire();        
        $carteBancaire->setStatus('En cours');
        $form = $this->createForm(CarteBancaireType::class, $carteBancaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image1 = $form->get('cinS1')->getData();
            $image2 = $form->get('cinS2')->getData();
            if ($image1) {
                $originalFilename = pathinfo($image1->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$image1->guessExtension();
                try {
                    $image1->move(
                        $this->getParameter('brochures_directory2'),
                        $newFilename
                    );
                } catch (FileException $e) {
                
                }
              $carteBancaire->setCinS1($newFilename);
            }
            
            if ($image2) {
                $originalFilename = pathinfo($image2->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$image2->guessExtension();
                try {
                    $image2->move(
                        $this->getParameter('brochures_directory2'),
                        $newFilename
                    );
                } catch (FileException $e) {
                
                }
              $carteBancaire->setCinS2($newFilename);
            }
            $carteBancaireRepository->save($carteBancaire, true);

            return $this->redirectToRoute('app_carte_bancaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('carte_bancaire/new.html.twig', [
            'carte_bancaire' => $carteBancaire,
            'form' => $form,
        ]);
    }
/***mrigl */
    #[Route('/{id}', name: 'app_carte_bancaire_show', methods: ['GET'])]
    public function show(CarteBancaire $carteBancaire): Response
    {
        return $this->render('carte_bancaire/show.html.twig', [
            'carte_bancaire' => $carteBancaire,
        ]);
    }
/***mrigl */
    #[Route('/{id}/edit', name: 'app_carte_bancaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CarteBancaire $carteBancaire, CarteBancaireRepository $carteBancaireRepository): Response
    {
        $form = $this->createForm(CarteBancaireType::class, $carteBancaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carteBancaireRepository->save($carteBancaire, true);

            return $this->redirectToRoute('app_carte_bancaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('carte_bancaire/edit.html.twig', [
            'carte_bancaire' => $carteBancaire,
            'form' => $form,
        ]);
    }
/***mrigl */
    #[Route('/{id}', name: 'app_carte_bancaire_delete', methods: ['POST'])]
    public function delete(Request $request, CarteBancaire $carteBancaire, CarteBancaireRepository $carteBancaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carteBancaire->getId(), $request->request->get('_token'))) {
            $carteBancaireRepository->remove($carteBancaire, true);
        }

        return $this->redirectToRoute('app_carte_bancaire_index', [], Response::HTTP_SEE_OTHER);
    }




}
