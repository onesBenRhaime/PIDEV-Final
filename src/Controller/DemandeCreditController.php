<?php

namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DemandeCredit;
use App\Entity\Credit;
use App\Entity\User;
use App\Form\DemandeCreditType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DemandeCreditRepository;
use App\Repository\UserRepository;
use App\Repository\CreditRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class DemandeCreditController extends AbstractController
{

    #[Route('/applyCredit/{id}', name: 'credit_create', methods: ['GET', 'POST'])]
    public function new(Request $request, DemandeCreditRepository $demandeCreditRepository ,SluggerInterface $slugger ,$id): Response
    {
        $demandeCredit = new DemandeCredit();
        $form = $this->createForm(DemandeCreditType::class, $demandeCredit);
        $form->handleRequest($request);
        $CreatedAt= new \DateTimeImmutable('now');
        $demandeCredit->setCreatedAt($CreatedAt);
        $demandeCredit->setStatus("in progress");
        $image1 = $form->get('cin1')->getData();
        $image2 = $form->get('cin2')->getData();
        if ($form->isSubmitted()) {
            
                        if ($image1) {
                            $originalFilename = pathinfo($image1->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'.'.$image1->guessExtension();
                            try {
                                $image1->move(
                                    $this->getParameter('brochures_directory3'),
                                    $newFilename
                                );
                            } catch (FileException $e) {
                            
                            }
                          $demandeCredit->setCin1($newFilename);
                        }                      
                        if ($image2) {
                            $originalFilename = pathinfo($image2->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'.'.$image2->guessExtension();
                        try {
                            $image2->move(
                              $this->getParameter('brochures_directory3'),
                                 $newFilename
                             );
                         } catch (FileException $e) {
                            
                        }
                          $demandeCredit->setCin2($newFilename);
                        }

            $demandeCreditRepository->save($demandeCredit, true);
        return $this->redirectToRoute('all_applies', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande_credit/demandeLoan.html.twig', [
           
            'form' => $form,
            'idCredit'=>$id,
        ]);
    }
    #[Route('/demande/credit', name: 'app_demande_credit')]
    public function index(): Response
    {
        return $this->render('demande_credit/index.html.twig', [
            'controller_name' => 'DemandeCreditController',
        ]);
    }
   
    #[Route('/editapplyCredit/{id}', name: 'editapplyCredit')]
    public function edit(Request $request,ManagerRegistry $doctrine, DemandeCreditRepository $demandeCreditRepository ,SluggerInterface $slugger ,$id) {
        $demandeCredit = new DemandeCredit();
        $demandeCredit = $this->getDoctrine()->getRepository(DemandeCredit::class)->find($id);
       
        $form = $this->createForm(DemandeCreditType::class,$demandeCredit);
        $CreatedAt= new \DateTimeImmutable('now');
        $demandeCredit->setCreatedAt($CreatedAt);
        $image1 = $form->get('cin1')->getData();
        $image2 = $form->get('cin2')->getData();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
       
            if ($image1) {
                $originalFilename = pathinfo($image1->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$image1->guessExtension();
                try {
                    $image1->move(
                        $this->getParameter('brochures_directory3'),
                        $newFilename
                    );
                } catch (FileException $e) {
                
                }
              $demandeCredit->setCin1($newFilename);
            }                      
            if ($image2) {
                $originalFilename = pathinfo($image2->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$image2->guessExtension();
            try {
                $image2->move(
                  $this->getParameter('brochures_directory3'),
                     $newFilename
                 );
             } catch (FileException $e) {
                
            }
              $demandeCredit->setCin2($newFilename);
            }

$demandeCreditRepository->save($demandeCredit, true);
       
        return $this->redirectToRoute('all_applies', [], Response::HTTP_SEE_OTHER);
        }
       
        return $this->render('demande_credit/editDemandeCredit.html.twig', ['form' =>$form->createView()]);
        }

    
    #[Route('/Applies', name: 'all_applies')]
    public function allApplies(EntityManagerInterface $entityManager,DemandeCreditRepository $repo,Request $request,PaginatorInterface $paginator): Response
    {
        $query = $this->getDoctrine()->getRepository(DemandeCredit::class)->createQueryBuilder('u')
        ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();

        $pagination = $paginator->paginate(

            $query,

            $request->query->getInt('page', 1),

            5 // items per page
            
        );
      
       
        return $this->render('demande_credit/credits.html.twig', [
            'pagination' => $pagination
        ]);
      
    }

    // #[Route('/allApplies', name: 'applies')]
    // public function Applies(EntityManagerInterface $entityManager,DemandeCreditRepository $repo,Request $request): Response
    // {
    //     $repository = $entityManager->getRepository(DemandeCredit::class);
    //     $applies = $repository->createQueryBuilder('l')
    //         ->orderBy('l.createdAt', 'DESC')
    //         ->getQuery()
    //         ->getResult();
           

    //     return $this->render('demande_credit/allApplies.html.twig', [
    //         'applies' => $applies,
    //     ]);
        
      
    // }
    
    #[Route('/detailsCredit/{id}', name: 'credit_details')]
    public function details(DemandeCredit $DemandeCredit): Response
    {
        return $this->render('demande_credit/details.html.twig', [
            'DemandeCredit' => $DemandeCredit,
        ]);
    }
  
    #[Route('/credits', name: 'credits')]
    public function demandes(): Response
    {
        return $this->render('demande_credit/credits.html.twig', [
            'controller_name' => 'DemandeCreditController',
        ]);
    }
    #[Route('removeapplyCredit/{id}', name:'applyCredit_remove')]
    public function removeapplyCredit(ManagerRegistry $doctrine,$id):Response{

        $em=$doctrine->getManager();
        $repo=$doctrine->getRepository(DemandeCredit::class);
        $demandeCredit=$repo->find($id);
        $em->remove($demandeCredit);
        $em->flush();
        return $this->redirectToRoute('all_applies');
    
    }

    #[Route('/accept/{id}', name: 'acceptApply')]
    public function acceptApply(Request $request, UserRepository $userRepository, CsrfTokenManagerInterface $csrfTokenManager,MailerInterface  $mailer,DemandeCredit $demandeCredit, DemandeCreditRepository $demandeCreditRepository ,$id): Response
    {
        $token = $request->request->get('_token');
        $tokenId = 'accept' . $demandeCredit->getId();
        $user = $userRepository->find($demandeCredit->getUserId());
       
        if ($csrfTokenManager->isTokenValid(new CsrfToken($tokenId, $token))) {
        $demandeCredit->setStatus('accepted');
        $email = (new Email())
        ->from('MazeBank199@gmail.com')
        ->to($user->getEmail())
        ->subject('demande de crédit')
        ->text("Votre demande de crédit a été acceptée ");
        $mailer->send($email);
        $demandeCreditRepository->save($demandeCredit, true);
    }

    return $this->redirectToRoute('applies');
}
#[Route('/reject/{id}', name: 'rejectApply')]
public function rejectApply(Request $request,MailerInterface  $mailer, UserRepository $userRepository,CsrfTokenManagerInterface $csrfTokenManager,DemandeCredit $demandeCredit, DemandeCreditRepository $demandeCreditRepository ,$id): Response
{
    $token = $request->request->get('_token');
    $tokenId = 'accept' . $demandeCredit->getId();
    $user = $userRepository->find($demandeCredit->getUserId());

if ($csrfTokenManager->isTokenValid(new CsrfToken($tokenId, $token))) {
    $demandeCredit->setStatus('rejected');
    $email = (new Email())
        ->from('MazeBank199@gmail.com')
        ->to($user->getEmail())
        ->subject('demande de crédit')
        ->text("Votre demande de crédit a été rejetée ");
        $mailer->send($email);
    $demandeCreditRepository->save($demandeCredit, true);
}

return $this->redirectToRoute('applies');
}

#[Route('/calculateMonthlyPayment/{id}', name: 'calculateMonthlyPayment')]
public function calculateMonthlyPayment(Request $request,$id,DemandeCredit $DemandeCredit): Response
{
    $demandeCredit = $this->getDoctrine()->getRepository(DemandeCredit::class)->find($id);
    $amount = $demandeCredit->getAmount();
    $months = $demandeCredit->getCreditId()->getMonths();
    $loanRate = $demandeCredit->getCreditId()->getLoanRate();
   
    // Calculate the monthly payment
    $monthlyPayment = $amount * ($loanRate * ((1 + $loanRate) ** $months)) / (((1 + $loanRate) ** $months) - 1);

    // Render the result as a response
    
    return $this->render('demande_credit/result.html.twig', [
        'monthlyPayment' => $monthlyPayment,
        'DemandeCredit' => $DemandeCredit,

    ]);
}
     
#[Route('/allApplies', name: 'applies')]
public function search(Request $request,PaginatorInterface $paginator)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $parameter1 = $request->query->get('parameter1');
        $parameter2 = $request->query->get('parameter2');
        $parameter3 = $request->query->get('parameter3');

        $queryBuilder->select('e')
            ->from(DemandeCredit::class, 'e')
            ->where('e.amount LIKE :parameter1')
            ->andWhere('e.status LIKE :parameter2')
            ->andWhere('e.createdAt LIKE :parameter3')
            ->orderBy('e.createdAt', 'DESC');
        

        $queryBuilder->setParameter('parameter1', '%' . $parameter1 . '%')
            ->setParameter('parameter2', '%' . $parameter2 . '%')
            ->setParameter('parameter3', '%' . $parameter3 . '%');

            $query = $queryBuilder->getQuery();
            $results = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            );

        return $this->render('demande_credit/allApplies.html.twig', [
            'results' => $results,
            'parameter1' => $parameter1,
            'parameter2' => $parameter2,
            'parameter3' => $parameter3,
         


        ]);
    }

    /*********************************************JSON*********************************************************/
    #[Route("/demandes", name: "list")]
    
    public function getApplies(DemandeCreditRepository $repo, SerializerInterface $serializer)
    {
        $applies = $repo->findAll();
        $json = $serializer->serialize($applies, 'json', ['groups' =>["credits",  "demandes","credit_categories","users"]]);
        return new Response($json);
    }

    #[Route("/DemandeCredit/{id}", name: "DemandeCredit")]
    public function DemandeCreditId($id, NormalizerInterface $normalizer, DemandeCreditRepository $repo)
    {
        $demande = $repo->find($id);
        $demandeNormalises = $normalizer->normalize($demande, 'json', ['groups' => "demandes"]);
        return new Response(json_encode($demandeNormalises));
    }


    #[Route("addDemandeCreditJSON/{id}", name: "addDemandeCreditJSON",methods: ['POST'])]
    public function addDemandeCreditJSON(Request $req,NormalizerInterface $Normalizer,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $demandeCredit = new DemandeCredit();
        $CreatedAt= new \DateTimeImmutable('now');
        $amount = $req->get('amount');
        $demandeCredit->setAmount((int)$amount);
        $note = $req->get('note');
        $demandeCredit->setNote((string)$note);
        $cin1 = $req->get('cin1');
        $demandeCredit->setCin1((string)$cin1);
        $cin2 = $req->get('cin2');
        $demandeCredit->setCin2((string)$cin2);
        $demandeCredit->setCreatedAt($CreatedAt);
        $demandeCredit->setStatus("in progress");
        $credit = $this->getDoctrine()->getRepository(Credit::class)->find($id);
        $demandeCredit->setCreditId($credit);
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
        $demandeCredit->setUserId($user);
       
        
        
        $em->persist($demandeCredit);
        $em->flush();

        $jsonContent = $Normalizer->normalize($demandeCredit, 'json', ['groups' => 'demandes']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("updateDemandeCreditJSON/{id}", name: "updateDemandeCreditJSON",methods: ['POST'])]
    public function updateDemandeCreditJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $demandeCredit = $em->getRepository(DemandeCredit::class)->find($id);
        
       
        $note = $req->get('note');
        $demandeCredit->setNote((string)$note);
       

        $em->flush();

        $jsonContent = $Normalizer->normalize($demandeCredit, 'json', ['groups' => 'demandes']);
        return new Response("demandeCredit updated successfully " );
    }

    #[Route("deleteDemandeCreditJSON/{id}", name: "deleteDemandeCreditJSON")]
    public function deleteDemandeCreditJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $demandeCredit = $em->getRepository(DemandeCredit::class)->find($id);
        $em->remove($demandeCredit);
        $em->flush();
        $jsonContent = $Normalizer->normalize($demandeCredit, 'json', ['groups' => 'demandes']);
        return new Response("DemandeCredit deleted successfully " . json_encode($jsonContent));
    }
}
