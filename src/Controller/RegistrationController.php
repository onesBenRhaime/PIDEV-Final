<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use App\Form\TwoFactorCodeFormType;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Symfony\Component\HttpFoundation\JsonResponse;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager, 
        GoogleAuthenticatorInterface $authenticator,
        SerializerInterface $serializer,
        SendMailService $mail): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
                
            );
            $user->setRoles(["ROLE_USER"]);

            // $secret = $authenticator->generateSecret();
            $secret = "4MZ2BTQTMKZ4K5O4M7JDSTJDRDU2X2HKU54I7ST4GHCDSAWNUODQ";
            $user->setGoogleAuthenticatorSecret($secret);
            $user->setStatus("enabled");
            
            $entityManager->persist($user);
            $entityManager->flush();

            $mail->send(
                'no-reply@mazebank.tn',
                $user->getEmail(),
                'Activation de votre compte',
                'register',
                compact('user')
            );

            $json = $serializer->serialize($user, 'json',['groups'=>'user']);
         

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
    
    #[Route('/list', name: 'list')]
    public function getUsers(UserRepository $repo,SerializerInterface $serializerInterface): Response 
    {
        $Users =$repo->findAll();
        // $json=$serializerInterface.serialize($Users,'json',['gourps'=>'students']); 
        // $jsonEncoder = new JsonEncoder();
        // $serializer = new Serializer([$normalizer], [$jsonEncoder]);
        $json = $serializerInterface->serialize($Users, 'json', [
            'groups' => ['User']
        ]);
        
        dump($json);
        die;

    }

    // #[Route('/add', name: 'add_user', methods:['POST'])]
    // public function adduser(Request $request,SerializerInterface $serializer,EntityManagerInterface $em) : Response 
    // {

    //     $content=$request->getContent();
    //     $user=$serializer->deserialize($content, User::class, 'json');
    //     $em->persist($user);
    //     $em->flush();
    //     return new Response('user added successfully');
    // }
    #[Route('/add', name: 'add_user', methods:['POST'])]
public function adduser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response 
{
    $content = $request->getContent();
    $user = $serializer->deserialize($content, User::class, 'json');
    
    $user->setPassword(
        $userPasswordHasher->hashPassword(
            $user,
            $user->getPassword()
        )
    );
    
    $em->persist($user);
    $em->flush();
    
    return new Response('user added successfully');
}


    #[Route('/user/signin', name: 'app_loging', methods: ['GET'])]
public function signinAction(Request $request, SerializerInterface $serializer): Response
{
    $email = $request->query->get("email");
    $password = $request->query->get("password");

    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

    if ($user) {
        if (password_verify($password, $user->getPassword())) {
            $data = $serializer->serialize($user, 'json', ['groups' => 'User']);
            return new Response($data, 200, [
                'Content-Type' => 'application/json'
            ]);
        } else {
            return new Response(json_encode(['message' => 'password not found']), 404, [
                'Content-Type' => 'application/json'
            ]);
        }
    } else {
        return new Response(json_encode(['message' => 'user not found']), 404, [
            'Content-Type' => 'application/json'
        ]);
    }
}



}
