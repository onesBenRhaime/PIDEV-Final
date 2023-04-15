<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Form\AssuranceType;
use App\Repository\AssuranceRepository;
use App\Repository\EmbaucheRepository;
use App\Repository\InvestissementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/assurance')]
class AssuranceController extends AbstractController
{
    #[Route('/back', name: 'app_assurance_index', methods: ['GET'])]
    public function index(AssuranceRepository $assuranceRepository): Response
    {
        return $this->render('assurance/index.html.twig', [
            'assurances' => $assuranceRepository->findAll(),
        ]);
    }

    #[Route('/search', name: 'app_assurance_search', methods: ['POST'])]
    public function search(AssuranceRepository $assuranceRepository, Request $request): Response
{
    $assurances = "";

    if ($request->isMethod('POST') && $request->request->has('search')) {
        $searchTerm = $request->request->get('search');
        $data = $assuranceRepository->search($searchTerm);
    } else {
        $data = $assuranceRepository->findAll();
    }

    foreach ($data as $assurance) {
        $image = $assurance->getImage();
        $assurances .= '<tr><td>' . $assurance->getId() . '</td>'
        . '<td>' . $assurance->getLibelle() . '</td>'
        . '<td><img src="/uploads/brochures/'.$image.'" /></td>'
       . '<td>' . $assurance->getPartenaire() . '</td>'
        . '<td>' . $assurance->getType() . '</td>'
        . '<td>'
        . '<a href="' . $this->generateUrl('app_assurance_show', ['id' => $assurance->getId()]) . '">show</a>'
        . ' | '
        . '<a href="' . $this->generateUrl('app_assurance_edit', ['id' => $assurance->getId()]) . '">edit</a>'
        . '</td></tr>';

    }
    

    return new Response($assurances);
}
#[Route('/searchFront', name: 'app_assurance_search_client', methods: ['POST'])]
public function searchFront(AssuranceRepository $assuranceRepository, Request $request): Response
{
$assurances = "";

if ($request->isMethod('POST') && $request->request->has('search')) {
    $searchTerm = $request->request->get('search');
    $data = $assuranceRepository->search($searchTerm);
} else {
    $data = $assuranceRepository->findAll();
}
foreach ($data as $assurance) {
    $image = $assurance->getImage();
    $assurances .= '<div class="card col-md-4" style="width: 18rem;margin-top:50px;margin-bottom:50px">'
        . '<img class="card-img-top" src="/uploads/brochures/' . $image . '" alt="Card image cap">'
        . '<div class="card-body">'
        . '<h5 class="card-title">' . $assurance->getLibelle() . '</h5>'
        . '<p class="card-text">Partenaire: ' . $assurance->getPartenaire() . '</p>'
        . '<p class="card-text">Type: ' . $assurance->getType() . '</p>'
        . '<a class="btn btn-primary" href="' . $this->generateUrl('app_assurance_show_front', ['id' => $assurance->getId()]) . '">show</a>'
        . '<a class="btn btn-primary" href="' . $this->generateUrl('app_assurance_edit_front', ['id' => $assurance->getId()]) . '">edit</a>'
        . '</div>'
        . '</div>';
}


return new Response($assurances);
}


    #[Route('/front', name: 'app_assurance_index_front', methods: ['GET'])]
    public function index_front(AssuranceRepository $assuranceRepository): Response
    {
        return $this->render('assurance/index_front.html.twig', [
            'assurances' => $assuranceRepository->findAll(),
        ]);
    }


    #[Route('/new_front', name: 'app_assurance_new_front', methods: ['GET', 'POST'])]
    public function new_front(Request $request, AssuranceRepository $assuranceRepository): Response
    {
        $assurance = new Assurance();
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assuranceRepository->save($assurance, true);

            return $this->redirectToRoute('app_assurance_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('assurance/new_front.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_assurance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AssuranceRepository $assuranceRepository,  SluggerInterface $slugger)
    {
        $assurance = new Assurance();
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                
                $assurance->setBrochureFilename($newFilename);
                $assuranceRepository->save($assurance, true);
            }
            return $this->redirectToRoute('app_assurance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('assurance/new.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    #[Route('/statistiques_offres', name: 'app_stats_offres', methods: ['GET'])]
    public function statistiques_offres(AssuranceRepository $assuranceRepository, EmbaucheRepository $embaucheRepository, InvestissementRepository $investissementRepository): Response
    {
       $assurances = $assuranceRepository->findAll();
       $embauches = $embaucheRepository->findAll();
       $investissements = $investissementRepository->findAll();
       $statistiques = [
        ["name" => "Assurance","nbre" => count($assurances)],
        ["name" => "Embauches", "nbre" => count($embauches)],
        ["name" => "Investissements", "nbre" => count($investissements)],
       ];
       return $this->renderForm('assurance/statistiques.html.twig', [
        'statistiques' => $statistiques
    ]);
    }

    #[Route('/{id}', name: 'app_assurance_show', methods: ['GET'])]
    public function show(Assurance $assurance): Response
    {
        return $this->render('assurance/show.html.twig', [
            'assurance' => $assurance,
        ]);
    }

    #[Route('/{id}/front', name: 'app_assurance_show_front', methods: ['GET'])]
    public function show_front(Assurance $assurance): Response
    {
        return $this->render('assurance/show_front.html.twig', [
            'assurance' => $assurance,
        ]);
    }


    #[Route('/{id}/edit/front', name: 'app_assurance_edit_front', methods: ['GET', 'POST'])]
    public function edit_front(Request $request, Assurance $assurance, AssuranceRepository $assuranceRepository): Response
    {
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assuranceRepository->save($assurance, true);

            return $this->redirectToRoute('app_assurance_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('assurance/edit_front.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_assurance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Assurance $assurance, AssuranceRepository $assuranceRepository,  SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                
                $assurance->setBrochureFilename($newFilename);
                $assuranceRepository->save($assurance, true);
            }
            $assuranceRepository->save($assurance, true);

            return $this->redirectToRoute('app_assurance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('assurance/edit.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    
    #[Route('/{id}/front', name: 'app_assurance_delete_front', methods: ['POST'])]
    public function delete_front(Request $request, Assurance $assurance, AssuranceRepository $assuranceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$assurance->getId(), $request->request->get('_token'))) {
            $assuranceRepository->remove($assurance, true);
        }

        return $this->redirectToRoute('app_assurance_index_front', [], Response::HTTP_SEE_OTHER);
    }
    

    #[Route('/{id}', name: 'app_assurance_delete', methods: ['POST'])]
    public function delete(Request $request, Assurance $assurance, AssuranceRepository $assuranceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$assurance->getId(), $request->request->get('_token'))) {
            $assuranceRepository->remove($assurance, true);
        }

        return $this->redirectToRoute('app_assurance_index', [], Response::HTTP_SEE_OTHER);
    }

    #

    
}
