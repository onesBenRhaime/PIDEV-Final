<?php

namespace App\Controller;

use App\Entity\TypeCompte;
use App\Form\TypeCompteType;
use App\Repository\TypeCompteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
#[Route('/TypeCompte')]
class TypeCompteController extends AbstractController
{
    #[Route('/AllTypeCompte', name: 'typeCompte_index', methods: ['GET'])]
    public function index(TypeCompteRepository $typeCompteRepository): Response
    {
        return $this->render('type_compte/index.html.twig', [
            'type_comptes' => $typeCompteRepository->findAll(),
        ]);
    }   
    /***Client */
    #[Route('/AllType', name: 'All_types', methods: ['GET'])]
    public function All_types(TypeCompteRepository $typeCompteRepository): Response
    {
        return $this->render('type_compte/all_types.html.twig', [
            'type_comptes' => $typeCompteRepository->findAll(),
        ]);
    }
    #[Route('/addTypeCompte', name: 'typeCompte_create', methods: ['GET', 'POST'])]
    public function new(Request $request, TypeCompteRepository $typeCompteRepository): Response
    {
        $typeCompte = new TypeCompte();
        $form = $this->createForm(TypeCompteType::class, $typeCompte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeCompteRepository->save($typeCompte, true);

            return $this->redirectToRoute('typeCompte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_compte/new.html.twig', [
            'type_compte' => $typeCompte,
            'form' => $form,
        ]);
    }

    #[Route('delete/{id}', name: 'typeCompte_delete', methods: ['POST'])]
    public function delete(Request $request, TypeCompte $typeCompte, TypeCompteRepository $typeCompteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeCompte->getId(), $request->request->get('_token'))) {
            $typeCompteRepository->remove($typeCompte, true);
        }

        return $this->redirectToRoute('typeCompte_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('edit/{id}', name: 'typeCompte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeCompte $typeCompte, TypeCompteRepository $typeCompteRepository): Response
    {
        $form = $this->createForm(TypeCompteType::class, $typeCompte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeCompteRepository->save($typeCompte, true);

            return $this->redirectToRoute('typeCompte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_compte/edit.html.twig', [
            'type_compte' => $typeCompte,
            'form' => $form,
        ]);
    }  

   
    #[Route('show/{id}', name: 'typeCompte_show', methods: ['GET'])]
    public function show(TypeCompte $typeCompte): Response
    {
        return $this->render('type_compte/show.html.twig', [
            'type_compte' => $typeCompte,
        ]);
    }

     /*************************JSON********************************/
    
     #[Route('/AllTypeCompteJson', name: 'list', methods: ['GET'])]
     public function allTypeComptesJson(TypeCompteRepository $typeCompteRepository  , SerializerInterface $serialiser)
     {
        $typesComptes = $typeCompteRepository->findAll();
        $json=$serialiser->serialize($typesComptes ,'json',['groups'=>"types"]);
        //    $typesComptesNormalises = $normalizer->normalize($typesComptes,'json',['groups'=>"types"]);
       //    $json = json_encode($typesComptesNormalises);
        return new Response ($json);             
     }

     #[Route("/type/{id}", name: "typeCarte")]
     public function typeTypeCompteJSON($id, NormalizerInterface $normalizer, TypeCompteRepository $repo)
     {
         $typeCompte = $repo->find($id);
         $typeCompteNormalises = $normalizer->normalize($typeCompte, 'json', ['groups' => "types"]);
         return new Response(json_encode($typeCompteNormalises));
     }
 
     #[Route("addTypeCompteJSON/new", name: "addTypeCompteJSON")]
     public function addTypeCompteJSON(Request $req,   NormalizerInterface $Normalizer)
     {
 
         $em = $this->getDoctrine()->getManager();
         $typeCompte = new TypeCompte();
         $typeCompte->setType($req->get('type'));
         $typeCompte->setDescription($req->get('description'));
         $em->persist($typeCompte);
         $em->flush();
 
         $jsonContent = $Normalizer->normalize($typeCompte, 'json', ['groups' => "types"]);
         return new Response(json_encode($jsonContent));
     }
 
     #[Route("updateTypeCompteJSON/{id}", name: "updateTypeCompteJSON")]
     public function updateTypeCompteJSON(Request $req, $id, NormalizerInterface $Normalizer)
     {
 
         $em = $this->getDoctrine()->getManager();
         $typeCompte = $em->getRepository(TypeCompte::class)->find($id);
         $typeCompte->setType($req->get('type'));
         $typeCompte->setDescription($req->get('description'));
 
         $em->flush();
 
         $jsonContent = $Normalizer->normalize($typeCompte, 'json', ['groups' => 'types']);
         return new Response("Type Compte  updated successfully " . json_encode($jsonContent));
     }
 
     #[Route("deleteTypeCompteJSON/{id}", name: "deleteTypeCompteJSON")]
     public function deleteTypeCompteJSON(Request $req, $id, NormalizerInterface $Normalizer)
     { 
         $em = $this->getDoctrine()->getManager();
         $typeCompte = $em->getRepository(TypeCompte::class)->find($id);
         $em->remove($typeCompte);
         $em->flush();
         $jsonContent = $Normalizer->normalize($typeCompte, 'json', ['groups' => 'types']);
         return new Response("Type Compte deleted successfully " . json_encode($jsonContent));
     }
 /********************JSON******************** */
   
}
