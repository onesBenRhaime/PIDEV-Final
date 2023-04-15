<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CarnetChequeRepository;
use App\Repository\TypeCarnetRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CarnetCheque;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\PieChart\PieSlice;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;


#[Route('/list/demande/carnet')]
class ListDemandeCarnetController extends AbstractController
{


    #[Route('/stats2', name: 'app_produit_stats2')]
    public function stats2(Request $request, CarnetChequeRepository $CarnetChequeRepository, TypeCarnetRepository $TypeCarnetRepository): Response
    {
        $carnetcheque = new CarnetCheque();
    
        $carnetcheque = [];
    
        $carnetcheque = $CarnetChequeRepository->getStat();
        $prods = array(array("TypeCarnet", "Nombre de demandes "));
        $i = 1;
        foreach ($carnetcheque as $prod) {
            $prods[$i] = array($prod["nom"], $prod["nbre"]);
            $i = $i + 1;
        }
    
      
        $pieChart = new Piechart();
    
    
    
        $pieChart->getData()->setArrayToDataTable($prods);
        $pieChart->getOptions()->setTitle('Demandes Carnet par Types');
        $pieChart->getOptions()->setHeight(600);
        $pieChart->getOptions()->setWidth(600);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#07600');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);

    
        return $this->render('statistiquecarnet.html.twig', [
         
            'pieChart' => $pieChart,
    
        ]);
    }

      
    #[Route('/', name: 'app_list_demande_carnet')]
    public function index(): Response
    {
        return $this->render('list_demande_carnet/index.html.twig', [
            'controller_name' => 'ListDemandeCarnetController',
        ]);
    }
 
    // #[Route('/carnetdetails', name: 'carnetdetails')]
    // public function admindetails(): Response
    // {
    //     return $this->render('list_demande_carnet/detailscarnet.html.twig', [
    //         'controller_name' => 'ListDemandeCarnetController',
    //     ]);
    // }
    #[Route('/carnetdetails/{id}', name: 'carnetdetails', methods: ['GET'])]
    public function show(CarnetCheque $carnetcheque): Response
    {
        return $this->render('list_demande_carnet/detailscarnet.html.twig', [
            'carnet' => $carnetcheque,
        ]);
    }

    #[Route('/servicecarnet', name: 'servicecarnet', methods: ['GET'])]
    public function ListDemendeCarnets(CarnetChequeRepository $CarnetChequeRepository): Response
    {
        return $this->render('list_demande_carnet/index.html.twig', [          
            'carnets' => $CarnetChequeRepository->findAll(),
        ]);
    }
    #[Route('accept/{id}', name: 'acceptcarnet', methods: ['POST'])]
    public function accept(Request $request, CarnetCheque $carnetcheque, CarnetChequeRepository $CarnetChequeRepository): Response
    {
        if ($this->isCsrfTokenValid('Approve'.$carnetcheque->getId(), $request->request->get('_token'))) {
              $carnetcheque->setStatus('accepté');
              $CarnetChequeRepository->save($carnetcheque, true);

        }

        return $this->redirectToRoute('servicecarnet', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('refuser/{id}', name: 'refusercarnet', methods: ['POST'])]
    public function refuser(Request $request, CarnetCheque $carnetcheque, CarnetChequeRepository $CarnetChequeRepository): Response
    {
        if ($this->isCsrfTokenValid('Reject'.$carnetcheque->getId(), $request->request->get('_token'))) {
              $carnetcheque->setStatus('refusé');
              $CarnetChequeRepository->save($carnetcheque, true);

        }

        return $this->redirectToRoute('servicecarnet', [], Response::HTTP_SEE_OTHER);
    }
    
       // *******************Json**********************


       #[Route('/ListeDemande/CarnetJson', name: 'AllListeDemandeCarnetJson')]
       public function AllListeDemandeCarnetJson(CarnetChequeRepository $CarnetChequeRepository, SerializerInterface $serializer)
       {
           $CarnetCheque = $CarnetChequeRepository->findAll();
           $json = $serializer->serialize($CarnetCheque, 'json', ['groups' => "CarnetCheque"]);
           return new Response($json);
       }
   
       #[Route("/ListeDemandeCarnetByIdJson/{id}", name: "ListeDemandeCarnetByIdJson")]
       public function ListeDemandeByIdJson($id, NormalizerInterface $normalizer, CarnetChequeRepository $CarnetChequeRepository)
       {
           $CarnetCheque = $CarnetChequeRepository->find($id);
           $CarnetChequeNormalises = $normalizer->normalize($CarnetCheque, 'json', ['groups' => "CarnetCheque"]);
           return new Response(json_encode($CarnetChequeNormalises));
       }
 
       

}
