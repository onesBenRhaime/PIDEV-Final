<?php

namespace App\Controller;

use App\Entity\Embauche;
use App\Form\EmbaucheType;
use App\Repository\EmbaucheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/embauche')]
class EmbaucheController extends AbstractController
{
    #[Route('/back', name: 'app_embauche_index', methods: ['GET'])]
    public function index(EmbaucheRepository $embaucheRepository): Response
    {
        return $this->render('embauche/index.html.twig', [
            'embauches' => $embaucheRepository->findAll(),
        ]);
    }

    #[Route('/front', name: 'app_embauche_index_front', methods: ['GET'])]
    public function index_front(EmbaucheRepository $embaucheRepository): Response
    {
        return $this->render('embauche/index_front.html.twig', [
            'embauches' => $embaucheRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_embauche_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmbaucheRepository $embaucheRepository, ValidatorInterface $validator): Response
    {
        $embauche = new Embauche();
        $form = $this->createForm(EmbaucheType::class, $embauche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $embaucheRepository->save($embauche, true);

            return $this->redirectToRoute('app_embauche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('embauche/new.html.twig', [
            'embauche' => $embauche,
            'form' => $form,
        ]);
    }

    #[Route('/new_front', name: 'app_embauche_new_front', methods: ['GET', 'POST'])]
    public function new_front(Request $request, EmbaucheRepository $embaucheRepository): Response
    {
        $embauche = new Embauche();
        $form = $this->createForm(EmbaucheType::class, $embauche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $embaucheRepository->save($embauche, true);

            return $this->redirectToRoute('app_embauche_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('embauche/new_front.html.twig', [
            'embauche' => $embauche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_embauche_show', methods: ['GET'])]
    public function show(Embauche $embauche): Response
    {
        return $this->render('embauche/show.html.twig', [
            'embauche' => $embauche,
        ]);
    }

    #[Route('/{id}/front', name: 'app_embauche_show_front', methods: ['GET'])]
    public function show_front(Embauche $embauche): Response
    {
        return $this->render('embauche/show_front.html.twig', [
            'embauche' => $embauche,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_embauche_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Embauche $embauche, EmbaucheRepository $embaucheRepository): Response
    {
        $form = $this->createForm(EmbaucheType::class, $embauche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $embaucheRepository->save($embauche, true);

            return $this->redirectToRoute('app_embauche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('embauche/edit.html.twig', [
            'embauche' => $embauche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit/front', name: 'app_embauche_edit_front', methods: ['GET', 'POST'])]
    public function edit_front(Request $request, Embauche $embauche, EmbaucheRepository $embaucheRepository): Response
    {
        $form = $this->createForm(EmbaucheType::class, $embauche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $embaucheRepository->save($embauche, true);

            return $this->redirectToRoute('app_embauche_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('embauche/edit_front.html.twig', [
            'embauche' => $embauche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_embauche_delete', methods: ['POST'])]
    public function delete(Request $request, Embauche $embauche, EmbaucheRepository $embaucheRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$embauche->getId(), $request->request->get('_token'))) {
            $embaucheRepository->remove($embauche, true);
        }

        return $this->redirectToRoute('app_embauche_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/front', name: 'app_embauche_delete_front', methods: ['POST'])]
    public function delete_front(Request $request, Embauche $embauche, EmbaucheRepository $embaucheRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$embauche->getId(), $request->request->get('_token'))) {
            $embaucheRepository->remove($embauche, true);
        }

        return $this->redirectToRoute('app_embauche_index_front', [], Response::HTTP_SEE_OTHER);
    }
}
