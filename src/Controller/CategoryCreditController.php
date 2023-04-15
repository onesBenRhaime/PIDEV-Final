<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CategoryCredit;
use App\Form\CategoryCreditType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryCreditRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class CategoryCreditController extends AbstractController
{
    #[Route('/categories', name: 'categories')]
    public function getcategories(CategoryCreditRepository $repo,PaginatorInterface $paginator,Request $request):Response{
        
        $query = $repo->createQueryBuilder('c')
        ->orderBy('c.name', 'DESC')
        ->getQuery();
        $categories = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        2
    );
        return $this->render('CategoryCredit/categories.html.twig', [
            'categories'=> $categories
        ]);
    }


    #[Route('/addcategory', name: 'category_add')]
    public function new(Request $request,ManagerRegistry $doctrine): Response
    {
       
           $category = new CategoryCredit();
           $form = $this->createForm(CategoryCreditType::class,$category);
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager = $doctrine->getManager();
            $repo=$doctrine->getRepository(CategoryCredit::class);
            // dump($category);
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('categories');
           } 
           return $this->renderForm('CategoryCredit/createCategory.html.twig',['form' => $form]);
    }

    #[Route('/editcategory/{id}', name: 'editcategory')]
    public function edit(Request $request,ManagerRegistry $doctrine ,$id) {
        $category = new CategoryCredit();
        $category = $this->getDoctrine()->getRepository(CategoryCredit::class)->find($id);
       
        $form = $this->createForm(CategoryCreditType::class,$category);
       
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
       
        $entityManager = $doctrine->getManager();
        $entityManager->flush();
       
        return $this->redirectToRoute('categories');
        }
       
        return $this->render('CategoryCredit/editCategory.html.twig', ['form' =>$form->createView()]);
        }
    
    
    #[Route('removeCategory/{id}', name:'category_remove')]
    public function removecategory(ManagerRegistry $doctrine,$id):Response{

        $em=$doctrine->getManager();
        $repo=$doctrine->getRepository(CategoryCredit::class);
        $category=$repo->find($id);
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('categories');
    
    }
}

