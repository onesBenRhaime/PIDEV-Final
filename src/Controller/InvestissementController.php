<?php

namespace App\Controller;

use App\Entity\Investissement;
use App\Form\InvestissementType;
use App\Repository\InvestissementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/investissement')]
class InvestissementController extends AbstractController
{
    #[Route('/back', name: 'app_investissement_index', methods: ['GET'])]
    public function index(InvestissementRepository $investissementRepository): Response
    {
        return $this->render('investissement/index.html.twig', [
            'investissements' => $investissementRepository->findAll(),
        ]);
    }
    #[Route('/back/s', name: 'app_investissement_sort', methods: ['GET'])]
    public function sort(InvestissementRepository $investissementRepository): Response
    {
        $investissements = $investissementRepository->findAll();
        usort($investissements, function($a, $b){
            // var_dump($a->getMinBudget());
            return $a->getMinBudget() - $b->getMinBudget();
        });
        return $this->render('investissement/index.html.twig', [
            'investissements' => $investissements,
        ]);
    }
    #[Route('/front/s', name: 'app_investissement_sort_front', methods: ['GET'])]
    public function sortFront(InvestissementRepository $investissementRepository): Response
    {
        $investissements = $investissementRepository->findAll();
        usort($investissements, function($a, $b){
            return $a->getMinBudget() - $b->getMinBudget();
        });
        return $this->render('investissement/index_front.html.twig', [
            'investissements' => $investissements,
        ]);
    }
    

    #[Route('/front', name: 'app_investissement_index_front', methods: ['GET'])]
    public function index_front(InvestissementRepository $investissementRepository): Response
    {
        return $this->render('investissement/index_front.html.twig', [
            'investissements' => $investissementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_investissement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InvestissementRepository $investissementRepository): Response
    {
        $investissement = new Investissement();
        $form = $this->createForm(InvestissementType::class, $investissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $investissementRepository->save($investissement, true);

            return $this->redirectToRoute('app_investissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('investissement/new.html.twig', [
            'investissement' => $investissement,
            'form' => $form,
        ]);
    }
    #[Route('/new/front', name: 'app_investissement_new_front', methods: ['GET', 'POST'])]
    public function new_front(Request $request, InvestissementRepository $investissementRepository): Response
    {
        $investissement = new Investissement();
        $form = $this->createForm(InvestissementType::class, $investissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $investissementRepository->save($investissement, true);

            return $this->redirectToRoute('app_investissement_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('investissement/new_front.html.twig', [
            'investissement' => $investissement,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_investissement_show', methods: ['GET'])]
    public function show(Investissement $investissement): Response
    {
        return $this->render('investissement/show.html.twig', [
            'investissement' => $investissement,
        ]);
    }

    #[Route('/{id}/front', name: 'app_investissement_show_front', methods: ['GET'])]
    public function show_front(Investissement $investissement): Response
    {
        return $this->render('investissement/show_front.html.twig', [
            'investissement' => $investissement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_investissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Investissement $investissement, InvestissementRepository $investissementRepository): Response
    {
        $form = $this->createForm(InvestissementType::class, $investissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $investissementRepository->save($investissement, true);

            return $this->redirectToRoute('app_investissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('investissement/edit.html.twig', [
            'investissement' => $investissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit/front', name: 'app_investissement_edit_front', methods: ['GET', 'POST'])]
    public function edit_front(Request $request, Investissement $investissement, InvestissementRepository $investissementRepository): Response
    {
        $form = $this->createForm(InvestissementType::class, $investissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $investissementRepository->save($investissement, true);

            return $this->redirectToRoute('app_investissement_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('investissement/edit_front.html.twig', [
            'investissement' => $investissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_investissement_delete', methods: ['POST'])]
    public function delete(Request $request, Investissement $investissement, InvestissementRepository $investissementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$investissement->getId(), $request->request->get('_token'))) {
            $investissementRepository->remove($investissement, true);
        }

        return $this->redirectToRoute('app_investissement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/front', name: 'app_investissement_delete_front', methods: ['POST'])]
    public function delete_front(Request $request, Investissement $investissement, InvestissementRepository $investissementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$investissement->getId(), $request->request->get('_token'))) {
            $investissementRepository->remove($investissement, true);
        }

        return $this->redirectToRoute('app_investissement_index_front', [], Response::HTTP_SEE_OTHER);
    }

    
}
