<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\TypeCarnetRepository;
use App\Form\TypeCarnetType;
use App\Entity\TypeCarnet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/type/carnet')]
class TypeCarnetController extends AbstractController
{
    #[Route('/', name: 'app_type_carnet')]
    public function index(): Response
    {
        return $this->render('type_carnet/index.html.twig', [
            'controller_name' => 'TypeCarnetController',
        ]);
    }
    
       
        #[Route('/showcarnets', name: 'showcarnets')]
        public function newcarnet(TypeCarnetRepository $TypeCarnetRepository ): Response
        {
            return $this->render('type_carnet/index.html.twig', [
                'carnets' => $TypeCarnetRepository->findAll(),
            ]);
        }
    
    
        // #[Route('/newtype', name: 'newtype')]
        // public function newtypecarnet(): Response
        // {
        //     return $this->render('type_carnet/ajoutertype.html.twig', [
        //         'controller_name' => 'TypeCarnetController',
        //     ]);
        // }
        // #[Route('/editcard', name: 'editcard')]
        // public function edittypecarnet(): Response
        // {
        //     return $this->render('type_carnet/modifiercarnet.html.twig', [
        //         'controller_name' => 'TypeCarnetController',
        //     ]);
        // }
    
   
    
        #[Route('/remove/{id}', name: 'carnet_remove')]
        public function removeCarnet(ManagerRegistry $doctrine,$id): Response
        {
            $em= $doctrine->getManager();
            $carnets= $doctrine->getRepository(TypeCarnet::class)->find($id);
            $em->remove($carnets);
            $em->flush();
            return $this->redirectToRoute('showcarnets');
        }
    
        #[Route('/addcarnet', name: 'typecarnet_add')]
        public function addtype(ManagerRegistry $doctrine,Request $req): Response {
            $em = $doctrine->getManager();
            $carnets = new TypeCarnet();
            $form = $this->createForm(TypeCarnetType::class,$carnets);
            $form->handleRequest($req);
            if ($form->isSubmitted() && $form->isValid()){
                $em->persist($carnets);
                $em->flush();
                return $this->redirectToRoute('showcarnets');
            }
          
            return $this->renderForm('type_carnet/ajoutertype.html.twig',['formCheque'=>$form]);
        }
    
        #[Route('/{id}', name: 'carnet_update')]
        public function updateCarnet(ManagerRegistry $doctrine,$id,Request $req): Response {
            $em = $doctrine->getManager();
            $carnets = $doctrine->getRepository(TypeCarnet::class)->find($id);
            $form = $this->createForm(TypeCarnetType::class,$carnets);
            $form->handleRequest($req);
            if($form->isSubmitted()&& $form->isValid()){
                $em->persist($carnets);
                $em->flush();
                return $this->redirectToRoute('showcarnets');
            }
            return $this->renderForm('type_carnet/modifiercarnet.html.twig',['carnet' => $carnets,'formCheque'=>$form]);
    
        }

            // *******************Json**********************


    #[Route('/AllTypes/CarnetJson', name: 'AllTypesCarnetJson')]
    public function AllTypesCarnetJson(TypeCarnetRepository $TypeCarnetRepository, SerializerInterface $serializer)
    {
        $TypeCarnet = $TypeCarnetRepository->findAll();
        $json = $serializer->serialize($TypeCarnet, 'json', ['groups' => "TypeCarnet"]);
        return new Response($json);
    }

    #[Route("/typeCarnetId/{id}", name: "typeCarnetId")]
    public function typeCarnetId($id, NormalizerInterface $normalizer, TypeCarnetRepository $TypeCarnetRepository)
    {
        $TypeCarnet = $TypeCarnetRepository->find($id);
        $TypeCarnetNormalises = $normalizer->normalize($TypeCarnet, 'json', ['groups' => "TypeCarnet"]);
        return new Response(json_encode($TypeCarnetNormalises));
    }
    #[Route("/updateTypeCarnetsJSON/{id}", name: "updateTypeCarnetsJSON")]
    public function updateTypeCarnetsJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $TypeCarnet = $em->getRepository(TypeCarnet::class)->find($id);
        $TypeCarnet->setNom($req->get('nom'));
        $TypeCarnet->setDescription($req->get('description'));
        $TypeCarnet->setStartnum($req->get('startnum'));
        $TypeCarnet->setEndnum($req->get('endnum'));
        $TypeCarnet->setDatecreation(new \DateTime($req->query->get('datecreation')));
        $TypeCarnet->setDatevalidation(new \DateTime($req->query->get('datevalidation')));
        // $dateString = $req->get('datecreation'); // Get the date string from the request
        // $date = \DateTime::createFromFormat('Y-m-d', $dateString); // Convert the date string to a DateTime object
        // $TypeCarnet->setDatecreation($date); // Set the DateTime object as the datecreation property of the TypeCarnet object
        // $dateString = $req->get('datevalidation'); // Get the date string from the request
        // $date = \DateTime::createFromFormat('Y-m-d', $dateString); // Convert the date string to a DateTime object
        // $TypeCarnet->setDatevalidation($date); // Set the DateTime object as the datecreation property of the TypeCarnet object
        
        // $TypeCarnet->setdatecreation($req->get('datecreation'));
        // $TypeCarnet->setdatevalidation($req->get('datevalidation'));
        $em->flush();

        $jsonContent = $Normalizer->normalize($TypeCarnet, 'json', ['groups' => 'TypeCarnet']);
        return new Response("Type Carnet updated successfully " . json_encode($jsonContent));
    }



    #[Route("/addTypeCarnets/JSON/new", name: "addTypeCarnets")]
    public function addTypeCarnets(Request $req,   NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $TypeCarnet = new TypeCarnet();
        $TypeCarnet->setNom($req->get('nom'));
        $TypeCarnet->setDescription($req->get('description'));
        $TypeCarnet->setStartnum($req->get('startnum'));
        $TypeCarnet->setEndnum($req->get('endnum'));
        $TypeCarnet->setDatecreation(new \DateTime($req->query->get('datecreation')));
        $TypeCarnet->setDatevalidation(new \DateTime($req->query->get('datevalidation')));
        $em->persist($TypeCarnet);
        $em->flush();
        $jsonContent = $Normalizer->normalize($TypeCarnet, 'json', ['groups' => 'TypeCarnet']);
        return new Response(json_encode($jsonContent));
        // return new Response(json_encode($jsonContent));


        // $serialize = new Serializer([new ObjectNormalizer()]);

        //     $formatted = $serialize->normalize("ReservationExcursiona ete supprimee avec success.");

        //     return new JsonResponse($formatted);

        // $jsonContent = $Normalizer->normalize($cartes, 'json', ['groups' => 'cartes']);
        // return new JsonResponse('added');
    }

    #[Route("/deleteTypesCarnetJSON/{id}", name: "deleteTypesCarnetJSON")]
    public function deleteTypesCarnetJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $TypeCarnet = $em->getRepository(TypeCarnet::class)->find($id);
        $em->remove($TypeCarnet);
        $em->flush();
        $jsonContent = $Normalizer->normalize($TypeCarnet, 'json', ['groups' => 'TypeCarnet']);
        return new Response("Carnet deleted successfully " . json_encode($jsonContent));
    }
    
}
