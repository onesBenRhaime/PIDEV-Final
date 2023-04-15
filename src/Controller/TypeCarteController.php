<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\TypeCarteRepository;
use App\Repository\CarteBancaireRepository;
use App\Form\TypeCarteType;
use App\Entity\TypeCarte;
use App\Entity\CarteBancaire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
// use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
// use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/type/carte')]
class TypeCarteController extends AbstractController
{



    #[Route('/', name: 'app_type_carte')]
    public function index(): Response
    {
        return $this->render('type_carte/index.html.twig', [
            'controller_name' => 'TypeCarteController',
        ]);
    }

    #[Route('/newcard', name: 'newcard')]
    public function newcard(TypeCarteRepository $TypeCarteRepository ): Response
    {
        return $this->render('type_carte/cards.html.twig', [
            'cards' => $TypeCarteRepository->findAll(),
        ]);
    }


    #[Route('/newtype', name: 'newtype')]
    public function newtype(): Response
    {
        return $this->render('type_carte/newtypecard.html.twig', [
            'controller_name' => 'TypeCarteController',
        ]);
    }
    #[Route('/editcard', name: 'editcard')]
    public function edittype(): Response
    {
        return $this->render('type_carte/editcard.html.twig', [
            'controller_name' => 'TypeCarteController',
        ]);
    }

    #[Route('/remove/{id}', name: 'card_remove')]
    public function removeCard(ManagerRegistry $doctrine,$id): Response
    {
        $em= $doctrine->getManager();
        $cards= $doctrine->getRepository(TypeCarte::class)->find($id);
        $em->remove($cards);
        $em->flush();
        return $this->redirectToRoute('newcard');
    }

    #[Route('/add', name: 'type_add')]
    public function addtype(ManagerRegistry $doctrine,Request $req): Response {
        $em = $doctrine->getManager();
        $cards = new TypeCarte();

        $form = $this->createForm(TypeCarteType::class,$cards);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cards);
            $em->flush();
            return $this->redirectToRoute('newcard');
        }
      
        return $this->renderForm('type_carte/newtypecard.html.twig',['formCard'=>$form]);
    }

    #[Route('/{id}', name: 'card_update')]
    public function updateClub(ManagerRegistry $doctrine,$id,Request $req): Response {
        $em = $doctrine->getManager();
        $cards = $doctrine->getRepository(TypeCarte::class)->find($id);
        $form = $this->createForm(TypeCarteType::class,$cards);
        $form->handleRequest($req);
        if($form->isSubmitted()&& $form->isValid()){
            $em->persist($cards);
            $em->flush();
            return $this->redirectToRoute('newcard');
        }
        return $this->renderForm('type_carte/editcard.html.twig',['card' => $cards,'formCard'=>$form]);

    }
    // *******************Json**********************


    #[Route('/AllTypes/CardJson', name: 'AllTypesCardJson')]
    public function AllTypesCardJson(TypeCarteRepository $TypeCarteRepository, SerializerInterface $serializer)
    {
        $TypeCartes = $TypeCarteRepository->findAll();
        $json = $serializer->serialize($TypeCartes, 'json', ['groups' => "TypeCartes"]);
        return new Response($json);
    }

    #[Route("/typeCarteId/{id}", name: "typeCarteId")]
    public function typeCarteId($id, NormalizerInterface $normalizer, TypeCarteRepository $TypeCarteRepository)
    {
        $TypeCartes = $TypeCarteRepository->find($id);
        $studentNormalises = $normalizer->normalize($TypeCartes, 'json', ['groups' => "TypeCartes"]);
        return new Response(json_encode($studentNormalises));
    }
    #[Route("/updateCartesJSON/{id}", name: "updateCartesJSON")]
    public function updateStudentJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $TypeCartes = $em->getRepository(TypeCarte::class)->find($id);
        $TypeCartes->setNom($req->get('nom'));
        $TypeCartes->setDescription($req->get('description'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($TypeCartes, 'json', ['groups' => 'TypeCartes']);
        return new Response("Carte updated successfully " . json_encode($jsonContent));
    }



    #[Route("/addCartes/JSON", name: "addCartesJSON")]
    public function addCartesJSON(Request $req,   NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $TypeCartes = new TypeCarte();
        $TypeCartes->setNom($req->get('nom'));
        $TypeCartes->setDescription($req->get('description'));
        $em->persist($TypeCartes);
        $em->flush();
        $jsonContent = $Normalizer->normalize($TypeCartes, 'json', ['groups' => 'TypeCartes']);
        return new Response(json_encode($jsonContent));


        // $serialize = new Serializer([new ObjectNormalizer()]);

        //     $formatted = $serialize->normalize("ReservationExcursiona ete supprimee avec success.");

        //     return new JsonResponse($formatted);

        // $jsonContent = $Normalizer->normalize($cartes, 'json', ['groups' => 'cartes']);
        // return new JsonResponse('added');
    }

    #[Route("/deleteTypesCartesJSON/{id}", name: "deleteTypesCartesJSON")]
    public function deleteTypesCartesJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $TypeCartes = $em->getRepository(TypeCarte::class)->find($id);
        $em->remove($TypeCartes);
        $em->flush();
        $jsonContent = $Normalizer->normalize($TypeCartes, 'json', ['groups' => 'TypeCartes']);
        return new Response("Carte deleted successfully " . json_encode($jsonContent));
    }

}
