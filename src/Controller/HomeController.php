<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Repository\ReclamationRepository;
use App\Repository\UserRepository;
use App\Repository\TypeReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ReclamationType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use App\Service\PdfService;
use App\Entity\Notification;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;


class HomeController extends AbstractController
{   
    //HOME PAGE
    #[Route('/home', name: 'app_home')]
    public function index(Security $security): Response
    {   
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/dashboard.html.twig');
        }
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_user_dashboard');
        } 
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    //USER LIST PAGE
    #[Route('/users-list', name: 'app_users_list')]
    public function usersList(UserRepository $repo): Response
    {   
        $users = $repo->findAll();
     return $this->render('home/users.html.twig',
     ['users'=>$users
     ]);
    }

    #[Route('/generate-pdf', name: 'app_generate_pdf')]
    public function generatePdfPersonne(PdfService $pdf, UserRepository $repo) {
    //     $users = $repo->findAll();
    //     $html = $this->renderView('home/user_table.html.twig', ['users'=>$users
    // ]);
    //     // $pdf->showPdfFile($html);
    $users = $repo->findAll();
    $html = $this->renderView('home/user_table.html.twig', ['users'=>$users]);

    // Generate PDF
    $pdfFile = $pdf->generateBinaryPDF($html);
    $pdf->showPdfFile($html);
    // // Save PDF to file
    // $pdfFilePath = 'C:/Users/Mega-PC/Desktop/EE';
    // file_put_contents($pdfFilePath, $pdfFile);

    // // Return the PDF file as a download
    // $response = new BinaryFileResponse($pdfFilePath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'file.pdf');
    return $response;
    }

    #[Route('/User/Status/{id}', name: 'Status')]
    public function DisableOrEnableUser(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(user::class);
        $User = $repo->find($id);
    
        if ($User->getStatus() === 'enabled') {
            $User->setStatus('disabled');
        } elseif ($User->getStatus() === 'disabled') {
            $User->setStatus('enabled');
        }
    
        $em->persist($User);
        $em->flush();
    
        return $this->redirectToRoute('app_users_list');
    }

    //ADMIN DASHBOARD
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(Security $security): Response
    {      $notifications = $this->getDoctrine()->getRepository(Notification::class)->findAll();

        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/dashboard.html.twig', [
                'controller_name' => 'AdminController',
                'notifications' => $notifications,
            ]);
        }
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_user_dashboard');
        } 

        return $this->redirectToRoute('app_login');
    }

    // #[Route('/blog', name: 'app_blog')]
    // public function blog(Security $security): Response
    // {      
    //     if ($security->isGranted('ROLE_ADMIN')) {
    //         return $this->redirectToRoute('app_admin');
    //     }
    //     if ($security->isGranted('ROLE_USER')) {
    //         return $this->redirectToRoute('app_user_dashboard');
    //     } 
        
    //     return $this->render('home/blog.html.twig');
    // }

    #[Route('/user-dashboard', name: 'app_user_dashboard')]
    public function userDashboard(Security $security, TexterInterface $texter, EntityManagerInterface $entityManager): Response
    {  
        if ($security->isGranted('ROLE_ADMIN')) 
        {
        return $this->render('admin/dashboard.html.twig');
        }
        if ($security->isGranted('ROLE_USER')) {
           

            /******ones */
/***Transaction */
$query = $entityManager->createQuery('SELECT t FROM App\Entity\Transaction t');
$transactions = $query->getResult();


$nbSM=0;

foreach ($transactions as $t) {
    if ($t->getTypeTransaction() == "Send Money") {
        $nbSM++;
    }
}
$nbV=0;

foreach ($transactions as $t) {
   if ($t->getTypeTransaction()=="Wire Transfer"){
      $nbV++;
   }
}
/****Account** */

$query = $entityManager->createQuery('SELECT a FROM App\Entity\Compte a');
$comptes = $query->getResult();

                

            /**ones  */
    
            
            return $this->render('home/user_dashboard.html.twig', [
                'transactionCount' =>count($transactions),
                'sendMoneyCount'  => $nbSM,
                'wireTransferCount'   =>$nbV,
                'accountCount'  =>count($comptes),
            ]);
        } 
        
        return $this->redirectToRoute('app_login');
    }

    #[Route('/admin', name: 'app_admin')]
    public function indexAdmin(): Response
    {   $notifications = $this->getDoctrine()->getRepository(Notification::class)->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
            'notifications' => $notifications,
        ]);
    }

    #[Route('/offer', name: 'app_offer')]
    public function offer(): Response
    {
        return $this->render('home/offer.html.twig');
    }
    
}
