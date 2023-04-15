<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\Compte;
use App\Form\TransactionType;
use App\Form\SendMoneyType;
use App\Repository\TransactionRepository;
use App\Repository\CompteRepository;
use App\Repository\AgenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

use ConsoleTVs\Charts\Classes\Chart;
use Doctrine\ORM\EntityManagerInterface;
use Chartisan\PHP\Chartisan;


#[Route('/webServices')]
class TransactionWebServices extends AbstractController
{
/*************************JSON********************************/
    
   #[Route('/allStatistique', name: 'statistique', methods: ['GET'])]
   public function Stat( TransactionRepository $repo  , SerializerInterface $serialiser)
    {
     $transactions = $repo->findAll();
     $transactionCount = count($transactions);
     return new Response($transactionCount);       
   }
    #[Route('/allTransactionsJson', name: 'listTransactions', methods: ['GET'])]
    public function allTransactionsJson( TransactionRepository $repo  , SerializerInterface $serialiser)
    {
       $transaction = $repo->findAll();
       $json=$serialiser->serialize($transaction ,'json',['groups'=>"transactions"]);
       return new Response ($json);             
    }


    

    
    #[Route("/type/{id}", name: "oneTransaction")]
    public function typeTransactionsJson($id, NormalizerInterface $normalizer, TransactionRepository $repo)
    {
        $transaction = $repo->find($id);
        $transactionNormalises = $normalizer->normalize($transaction, 'json', ['groups' => "transactions"]);
        return new Response(json_encode($transactionNormalises));
    }
    
    #[Route("/addTransactionsJson/new", name: "addTransactionsJson", methods: ['POST'])]
    public function addTransactionsJson(Request $req, NormalizerInterface $Normalizer,TransactionRepository $transactionRepository,CompteRepository $compteRepository )
    {
    
        $em = $this->getDoctrine()->getManager();
        $transaction = new Transaction();
       
        $transaction->setTypeTransaction($req->get('typeTransaction')); 
        $transaction->setRequestTo($req->get('requestTo'));     
        $transaction->setRequestFrom($req->get('requestFrom'));
        $transaction->setMontant($req->get('montant'));
        $transaction->setStatue('valide');
        $transaction->setAgenceName("no agance");
        $compteRepository = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $compteRepository->findOneBy([], ['id' => 'ASC']);
        $transaction->setCompte($compte);
        $transaction->setDate(new \DateTime('now'));       
        $transactionRepository->save($transaction, true);
    
       // $jsonContent = $Normalizer->normalize($transaction, 'json', ['groups' => "transactions"]);
        return new Response("Transaction  added successfully ");
    }
    
    #[Route("/updateTransactionsJson/{id}", name: "updateTransactionsJson",methods:["POST"])]
    public function updateTransactionsJson(Request $req, $id, NormalizerInterface $Normalizer)
    {
    
        $em = $this->getDoctrine()->getManager();
        $transaction = $em->getRepository(Transaction::class)->find($id);
        $transaction->setRequestTo($req->get('requestTo'));        
        $transaction->setMontant($req->get('montant'));
     
        $transaction->setDate(new \DateTime($req->query->get('date')));
    
        $em->flush();
    
        $jsonContent = $Normalizer->normalize($transaction, 'json', ['groups' => 'transactions']);
        return new Response("Transaction  updated successfully " . json_encode($jsonContent));
    }
    
    #[Route("/deleteTransactionsJson/{id}", name: "deleteTransactionsJson" ,methods:["POST"])]
    public function deleteTransactionsJson(Request $req, $id, NormalizerInterface $Normalizer)
    { 
        $em = $this->getDoctrine()->getManager();
        $transaction = $em->getRepository(Transaction::class)->find($id);
        $em->remove($transaction);
        $em->flush();
        $jsonContent = $Normalizer->normalize($transaction, 'json', ['groups' => 'transactions']);
        return new Response("Transaction deleted successfully ");
    }
    /********************JSON******************** */
    
}