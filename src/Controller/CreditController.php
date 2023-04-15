<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Credit;
use App\Form\CreditType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CreditRepository;
use App\Entity\CategoryCredit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;

class CreditController extends AbstractController
{

    #[Route('/loanPlans', name: 'loanplans')]
    public function loanPlans(CreditRepository $repo,PaginatorInterface $paginator,Request $request):Response{

        $query = $repo->createQueryBuilder('c')
        ->orderBy('c.minAmount', 'DESC')
        ->getQuery();
        $loanPlans = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        3
    );
    return $this->render('credit/loanPlans.html.twig', [
            'loanPlans'=> $loanPlans
        ]);
    }
    #[Route('/loans', name: 'loans')]
    public function list(Request $request,CreditRepository $repo)
    {
        $categoryId = $request->query->get('category');
        $plans=$repo->findByCategory($categoryId);
        $categories = $this->getDoctrine()
        ->getRepository(CategoryCredit::class)
        ->findAll();
        return $this->render('credit/loans.html.twig', [
            'plans'=> $plans,'categories'=>$categories
        ]);
    }
    // #[Route('/loans', name: 'loans')]
    // public function plans(CreditRepository $repo):Response{
    //     $plans=$repo->findAll();
    //     return $this->render('credit/loans.html.twig', [
    //         'plans'=> $plans
    //     ]);
    // }
    
    #[Route('/addLoanAdmin', name: 'addLoanAdmin')]
    public function addLoanAdmin(Request $request,ManagerRegistry $doctrine): Response
    {

           $credit = new Credit();
           $form = $this->createForm(CreditType::class,$credit);
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()) {
            $credit = $form->getData();
            $entityManager = $doctrine->getManager();
            $repo=$doctrine->getRepository(Credit::class);
            // dump($credit);
            $entityManager->persist($credit);
            $entityManager->flush();
            return $this->redirectToRoute('loanplans');
           } 
           return $this->renderForm('credit/addLoanAdmin.html.twig',['form' => $form]);
    }
    
    #[Route('/editcredit/{id}', name: 'editcredit')]
    public function edit(Request $request,ManagerRegistry $doctrine ,$id) {
        $credit = new credit();
        $credit = $this->getDoctrine()->getRepository(Credit::class)->find($id);
       
        $form = $this->createForm(CreditType::class,$credit);
       
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
       
        $entityManager = $doctrine->getManager();
        $entityManager->flush();
       
        return $this->redirectToRoute('loanplans');
        }
       
        return $this->render('credit/editLoan.html.twig', ['form' =>$form->createView()]);
        }

        #[Route('removeCredit/{id}', name:'credit_remove')]
        public function removecredit(ManagerRegistry $doctrine,$id):Response{
    
            $em=$doctrine->getManager();
            $repo=$doctrine->getRepository(Credit::class);
            $credit=$repo->find($id);
            $em->remove($credit);
            $em->flush();
            return $this->redirectToRoute('loanplans');
        
        }
    /*****************************JSON*******************************/
    #[Route('/loansJson', name: 'loansJson')]
    public function loansJson(CreditRepository $repo,SerializerInterface $serializer){
        $plans=$repo->findAll();
        $json = $serializer->serialize($plans, 'json',  ['groups' => ["credits", "credit_categories"]]);
        return new Response($json);

        
    }
}
