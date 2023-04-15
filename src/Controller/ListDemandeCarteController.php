<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CarteBancaireRepository;
use App\Repository\TypeCarteRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CarteBancaire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Dompdf\Dompdf;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\PieChart\PieSlice;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Builder\Builder;
use App\Services\QrcodeService;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use http\Message;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

use necrox87\NudityDetector\NudityDetector;
use Swift_Message;
#[Route('/list/demande/carte')]
class ListDemandeCarteController extends AbstractController
{
    #[Route('/', name: 'app_list_demande_carte')]
    public function index(): Response
    {
        return $this->render('list_demande_carte/index.html.twig', [
            'controller_name' => 'ListDemandeCarteController',
        ]);
    }
    // #[Route('/adminservice', name: 'service_admin')]
    // public function adminservice(): Response
    // {
    //     return $this->render('list_demande_carte/index.html.twig', [
    //         'controller_name' => 'ListDemandeCarteController',
    //     ]);
    // }
    // #[Route('/detailsadmin', name: 'serviceadmin_details')]
    // public function admindetails(): Response
    // {
    //     return $this->render('list_demande_carte/detailscarte.html.twig', [
    //         'controller_name' => 'ListDemandeCarteController',
    //     ]);
    // }
    #[Route('/pdf', name: 'carte-pdf', methods: ['GET'])]
    public function generatePdf()
    {
        $data = $this->getDoctrine()->getRepository(CarteBancaire::class)->findAll();
    
        $html = $this->renderView('list_demande_carte/listpdf.html.twig', [
            'cartes' => $data
        ]);
    
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
    
        $pdfOutput = $dompdf->output();
    
        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    #[Route('/servicecarte', name: 'servicecarte', methods: ['GET'])]
    public function ListDemendeCartes(CarteBancaireRepository $CarteBancaireRepository): Response
    {
        return $this->render('list_demande_carte/index.html.twig', [          
            'cartes' => $CarteBancaireRepository->findAll(),
        ]);
    }
    #[Route('/cartedetails/{id}', name: 'cartedetails', methods: ['GET'])]
    public function show(CarteBancaire $carteBancaire): Response
    {
        return $this->render('list_demande_carte/detailscarte.html.twig', [
            'carte' => $carteBancaire,
        ]);
    }

    #[Route('accept/{id}', name: 'acceptcarte', methods: ['POST'])]
    public function accept(QrcodeService $qrcodeService, MailerInterface $mailer, CsrfTokenManagerInterface $csrfTokenManager, Request $request, CarteBancaire $carteBancaire, CarteBancaireRepository $CarteBancaireRepository, $id): Response
    {
        if ($this->isCsrfTokenValid('Approve' . $carteBancaire->getId(), $request->request->get('_token'))) {
            $carteBancaire->setStatus('accepté');
            
            $message = (new \Swift_Message('Confirmation de réservation pour événement'));
            $img = $message->embed(\Swift_Image::fromPath('./QRcode/client'.'.png')); // your qr code
            $logo = $message->embed(\Swift_Image::fromPath('./mazebank.jpg')); // your qr code
          
    
            $qrCode = $qrcodeService->qrcode($carteBancaire);
    
            $email = (new Email())
                ->from('MazeBank199@gmail.com')
                ->to('kacem.salma@esprit.tn')
                ->subject('Demande de Carte')
                ->text("Votre demande de carte a été acceptée ")
               
                ->html(
                   
                    $this->renderView(
                        'emails/registration.html.twig',
                        [
                            'img'  =>$img,
                            'qrCode' => $qrCode,
                           
                         
                            
                        ]
                    )
                );
    
            if ($email->getTextBody() || $email->getHtmlBody() || count($email->getAttachments()) > 0) {
                $mailer->send($email);
                $CarteBancaireRepository->save($carteBancaire, true);
            } else {
                $this->addFlash('error', 'The email message is not valid. Please make sure it has a text or an HTML part or attachments.');
            }
        }
    
        return $this->redirectToRoute('servicecarte', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('refuser/{id}', name: 'refusercarte', methods: ['POST'])]
    public function refuser(QrcodeService $qrcodeService, MailerInterface $mailer, CsrfTokenManagerInterface $csrfTokenManager, Request $request, CarteBancaire $carteBancaire, CarteBancaireRepository $CarteBancaireRepository, $id): Response
    {
       if ($this->isCsrfTokenValid('Reject'.$carteBancaire->getId(), $request->request->get('_token'))) {
              $carteBancaire->setStatus('refusé');

             $message = (new \Swift_Message('Confirmation de demande'));
            $img = $message->embed(\Swift_Image::fromPath('./QRcode/client2'.'.png')); // your qr code
            //$logo = $message->embed(\Swift_Image::fromPath('./mazebank.jpg')); // your qr code
          
    
            $qrCode = $qrcodeService->qrcodee($carteBancaire);
    
            $email = (new Email())
                ->from('MazeBank199@gmail.com')
                ->to('kacem.salma@esprit.tn')
                ->subject('Demande de Carte')
                ->text("Votre demande de carte a été refusée ")
               
                ->html(
                   
                    $this->renderView(
                        'emails/registration.html.twig',
                        [
                            'img'  =>$img,
                            'qrCode' => $qrCode,
                           
                         
                            
                        ]
                    )
                );
    
            if ($email->getTextBody() || $email->getHtmlBody() || count($email->getAttachments()) > 0) {
                $mailer->send($email);
                $CarteBancaireRepository->save($carteBancaire, true);
            } else {
                $this->addFlash('error', 'The email message is not valid. Please make sure it has a text or an HTML part or attachments.');
            }
        }

        return $this->redirectToRoute('servicecarte', [], Response::HTTP_SEE_OTHER);
    }
 #[Route('/statis', name: 'statis')]
       public function stats(Request $request,CarteBancaireRepository $CarteBancaireRepository, TypeCarteRepository $typeCarteRepository): Response
       {
            $carteBancaire = new CarteBancaire();
    
            $carteBancaires = [];
    
               
                $carteBancaires = $CarteBancaireRepository->getStat();
                $prods = array (array("TypeCarte","Nombre de demandes de carte Bancaires"));
               $i = 1;
               foreach ($carteBancaires as $prod){
                   $prods[$i] = array($prod["nom"],$prod["nbre"]);
                   $i= $i + 1;
               }
   
               $bar = new Barchart();
               $pieChart = new Piechart();
               $bar->getData()->setArrayToDataTable($prods);
               $pieChart->getData()->setArrayToDataTable($prods);

               $bar->getOptions()->setTitle('Statistique de nombre de Demandes par Types');
               $bar->getOptions()->getHAxis()->setTitle('Statistique de nombre de Demandes par Types');
               $bar->getOptions()->getHAxis()->setMinValue(0);
               $bar->getOptions()->setWidth(900);
               $bar->getOptions()->setHeight(500);
   
               $pieChart->getOptions()->setTitle('Statistique de nombre de Demandes par Types');
               $pieChart->getOptions()->setHeight(400);
               $pieChart->getOptions()->setWidth(400);
               $pieChart->getOptions() ->getTitleTextStyle()->setColor('#07600');
               $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);
                
           return $this->render('statistiques.html.twig', [
               'bar' => $bar,
               'pieChart' => $pieChart,
   
           ]);
       }

       // *******************Json**********************


       #[Route('/ListeDemande/CarteJson', name: 'AllListeDemandeJson')]
       public function AllListeDemandeJson(CarteBancaireRepository $CarteBancaireRepository, SerializerInterface $serializer)
       {
           $CarteBancaire = $CarteBancaireRepository->findAll();
           $json = $serializer->serialize($CarteBancaire, 'json', ['groups' => "CarteBancaire"]);
           return new Response($json);
       }
   
       #[Route("/ListeDemandeCarteByIdJson/{id}", name: "ListeDemandeByIdJson")]
       public function ListeDemandeByIdJson($id, NormalizerInterface $normalizer, CarteBancaireRepository $CarteBancaireRepository)
       {
           $CarteBancaire = $CarteBancaireRepository->find($id);
           $CarteBancaireNormalises = $normalizer->normalize($CarteBancaire, 'json', ['groups' => "CarteBancaire"]);
           return new Response(json_encode($CarteBancaireNormalises));
       }
  

   
   
   
   
   }


