<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Service\SendMailService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\NotificationRepository;
use App\Entity\Notification;
use Doctrine\Persistence\ManagerRegistry;

class AuthController extends AbstractController
{   
    public function generateSecretCode(GoogleAuthenticatorInterface $authenticator): Response
    {
        $user = $this->getUser(); // Replace with your user object
        
        $qrCodeContent = $authenticator->getQRContent($user);
        // Use $qrCodeContent to generate a QR code image or link to a QR code generator
        
        return $this->render('secret_code.html.twig', [
            'qrCodeContent' => $qrCodeContent,
        ]);
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // User is already authenticated, redirect to the home page
            return $this->redirectToRoute('app_user_dashboard');
            
    
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
        
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/2fa', name: '2fa_login')]
    public function check2fa(GoogleAuthenticatorInterface $authenticator, TokenStorageInterface $storage)
    {
        $code = $authenticator->getQRContent($storage->getToken()->getUser());
        $qrCode = "http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=".$code;

        return $this->render('/security/2fa_login.html.twig', [
            'qrCode' => $qrCode
        ]);
    }


   #[Route(path: '/forgot', name: 'app_forgot')]
    public function forgottenPassword(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail
    ): Response
    {
        $form = $this->createForm(ResetPasswordFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            // On vérifie si on a un utilisateur
            if($user){
                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('app_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // On crée les données du mail
                $context = compact('url', 'user');

                // Envoi du mail
                $mail->send(
                    'bedouimehdi@mazebank.tn',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            // $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('/security/forgot.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route(path: '/forgot/{token}', name: 'app_reset')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $usersRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) : Response
    {   
        $user = $usersRepository->findOneByResetToken($token);
        if($user){
            $form = $this->createForm(ResetPasswordRequestFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                // On efface le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $message = 'User ' . $user->getName() . ' has reset their password.';
                $this->sendNotification($message);

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
            
        }
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }

    private function sendNotification(string $message): void
{
    $notification = new Notification();
    $notification->setMessage($message);

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($notification);
    $entityManager->flush();
}

#[Route('/notification/{id}/read', name: 'notification_read')]
public function markNotificationAsRead(Notification $notification, EntityManagerInterface $entityManager): Response
{
    $notification->setIsRead(true);
    $entityManager->flush();

    return $this->redirectToRoute('app_admin'); // Redirect to dashboard or any other page
}

private NotificationRepository $notificationRepository;

public function __construct(NotificationRepository $notificationRepository)
{
    $this->notificationRepository = $notificationRepository;
}

#[Route('/notification', name: 'notification')]
    public function usersList(NotificationRepository $repo): Response
    {   $notifications = $this->getDoctrine()->getRepository(Notification::class)->findAll();
        
        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notif/Delete/{id}', name: 'delete_notif')]
    public function deleteNotification(Request $request, EntityManagerInterface $entityManager,ManagerRegistry $doctrine, $id): Response
    {   $em= $doctrine->getManager();
        $repo= $doctrine->getRepository(Notification::class);
        $Notification= $repo->find($id);
        $em->remove($Notification);
        $entityManager->flush();
        
     
        return $this->redirectToRoute('app_admin');
    }
}


