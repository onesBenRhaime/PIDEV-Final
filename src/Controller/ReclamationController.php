<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Repository\ReclamationRepository;
use App\Repository\TypeReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ReclamationType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function reclamation(ReclamationRepository $repo, Security $security, AuthenticationUtils $authenticationUtils, SerializerInterface $serializer): Response
    {   
        
        if ($security->isGranted('ROLE_ADMIN')) {
            // $reclamations = $repo->findAll();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery
            (
                 'SELECT r, rt
                 FROM App:Reclamation r
                 JOIN r.TypeReclamation rt
                 '
            );
            $reclamations = $query->getResult();
            return $this->render('home/reclamation.html.twig',
    ['reclamations'=>$reclamations
    ]);
        }
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_user_dashboard');
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        // return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        return $this->redirectToRoute('app_login');
    }
    
    #[Route('/reclamation/remove/{id}', name: 'remove_reclamation')]
    public function removeReclamation(ManagerRegistry $doctrine,$id): Response
    {
        $em = $doctrine->getManager();
        $reclamation = $doctrine->getRepository(Reclamation::class)->find($id);
        
            $em->remove($reclamation);
            $em->flush();
            return $this->redirectToRoute('app_reclamation');
        
    }
    
    #[Route('/messages', name: 'app_messages')]
    public function messages(ManagerRegistry $doctrine,Request $req): Response
    {   $user = $this->getUser();
        $em = $doctrine->getManager();
        $reclamation = new Reclamation();
        $reclamation->setClientName($user);
        $form = $this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($reclamation);
            $em->flush();
            return $this->redirectToRoute('app_messages');
        }
        return $this->renderForm('home/messages.html.twig', [
            'form' => $form
        ]);
    }
}
