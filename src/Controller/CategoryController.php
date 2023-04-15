<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryType;
use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function category(CategoryRepository $repo): Response
    { 
        $categories = $repo->findAll();
        return $this->render('category/category.html.twig',
    ['categories'=>$categories
    ]);
    }

    #[Route('/category/remove/{id}', name: 'remove_category')]
    public function remove(ManagerRegistry $doctrine,$id): Response
    {
        $em = $doctrine->getManager();
        $category = $doctrine->getRepository(Category::class)->find($id);
        
            $em->remove($category);
            $em->flush();
            return $this->redirectToRoute('app_category');
        
    }



   #[Route('/category/update/{id}', name: 'update_category')]
    public function update(ManagerRegistry $doctrine,Request $request,$id): Response
    {
        $em = $doctrine->getManager();
        $category = $doctrine->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            
            $em->flush();
            return $this->redirectToRoute('app_category');
        }
        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/category/create', name: 'app_category_create')]
    public function categoryCreate(ManagerRegistry $doctrine,Request $request): Response
    {
        $em = $doctrine->getManager();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('app_category');
        }
        
        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }
}
