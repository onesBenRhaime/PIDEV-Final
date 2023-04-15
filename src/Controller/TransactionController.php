<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    /**********Client Route ************ */ 
    #[Route('/transactions', name: 'transactions')]
    public function index(): Response
    {
        return $this->render('transaction/wireTransferAll.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/detailsTransaction', name: 'transaction_details')]
    public function details(): Response
    {
        return $this->render('transaction/wireTransferDetails.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/createTransaction', name: 'transaction_create')]
    public function create(): Response
    {
        return $this->render('transaction/wireTransferCreate.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }
    /** Transfer ,send money */
    #[Route('/SendMoney', name: 'transaction_sendMoney')]
    public function sendMoney(): Response
    {
        return $this->render('transaction/sendMoney.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/beneficiaries', name: 'beneficiaries')]
    public function beneficiaries(): Response
    {
        return $this->render('transaction/beneficiaries.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/detailsbeneficiarie', name: 'beneficiarie_details')]
    public function detailsBenef(): Response
    {
        return $this->render('transaction/beneficiarieDetails.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/createbeneficiarie', name: 'beneficiarie_create')]
    public function createBenef(): Response
    {
        return $this->render('transaction/beneficiarieCreate.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }



    #[Route('/otherBank', name: 'otherBanks')]
    public function getAllotherBanks(): Response
    {
        return $this->render('transaction/otherBank.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/sendOtherBank', name: 'otherBank_send')]
    public function sendToOtherBank(): Response
    {
        return $this->render('transaction/otherBankSend.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

     /***USER */
    #[Route('/AllwireTransfer', name: 'wireTransfer_All')]
    public function getAllwireTransfer(): Response
    {
        return $this->render('transaction/wireTransferAll.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/createWireTransfer', name: 'wireTransfer_create')]
    public function CreateWireTransfer(): Response
    {
        return $this->render('transaction/wireTransferCreate.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }


    #[Route('/detailsWireTransfer', name: 'wireTransfer_details')]
    public function DetailsWireTransfer(): Response
    {
        return $this->render('transaction/wireTransferDetails.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

  
    #[Route('/transferHistory1', name: 'transfer_history123')]
    public function History(): Response
    {
        return $this->render('transaction/transferHistory.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/transactions1', name: 'transactions1')]
    public function transactions(): Response
    {
        return $this->render('transaction/transactions.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

      /***ADMIN */
      #[Route('/wire-transfer', name: 'wireTransfer_All_ADD')]
      public function ALLWireTransfer(): Response
      {
          return $this->render('transaction/wireTransfer_Ad.html.twig', [
              'controller_name' => 'TransactionController',
          ]);
      }
 
    #[Route('/Money-Request', name: 'moneyRequest_Ad')]
    public function ALLMoneyRequest(): Response
    {
        return $this->render('transaction/moneyRequest_Ad.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/Transactions-Ad', name: 'transactions_Ad')]
    public function AllTransactions(): Response
    {
        return $this->render('transaction/transactions_Ad.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }




}
