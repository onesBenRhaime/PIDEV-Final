<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Form\SendMoneyType;
use App\Repository\TransactionRepository;
use App\Repository\AgenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Dompdf\Dompdf;
use Twilio\Rest\Client;
use Symfony\Component\Notifier\TexterInterface;


#[Route('/transactions')]
class TransactionsController extends AbstractController
{

 

    // #[Route('/filtre', name: 'app_transactions_filtre', methods: ['POST'])]
    // public function filtreSec(Request $request, Transaction $transaction, TransactionRepository $transactionRepository): Response
    // {
    //     // $transactions = $transactionRepository->findAll();
    //     // $filtered_data = [];
    //     // foreach ($transactions as $transaction) {
    //     //     array_filter($transaction, function($item) {
    //     //         return $item['montant'] > 100;
    //     //      });
    //     // }
    //     $transactions = $transactionRepository->findAll();
    //     $filtered_data = [];
    //         array_filter($transactions, function($item) {

    //             return $item['statue'] =="valide";
    //          });      
   

    //   // Pass the filtered data to the Twig template
    //    return $this->render('transactions/transactionFiltre.html.twig', [
    //     'transactions' => $filtered_data
    //    ]);
    // }
    
//    #[Route('/statistiques', name: 'app_transactions_statistiques', methods: ['POST'])]
//     public function statistiques(Request $request, Transaction $transaction, TransactionRepository $transactionRepository): Response
//     {
    
//         $transactions = $transactionRepository->findAll();
//         $filtered_data = [];
//             array_filter($transactions, function($item) {

//                 return $item['statue'] =="valide";
//              });      
   

//       // Pass the filtered data to the Twig template
//        return $this->render('transactions/transactionFiltre.html.twig', [
//         'transactions' => $filtered_data
//        ]);
//     }
   
    #[Route('/pdf', name: 'transactions-pdf', methods: ['GET'])]   
    public function generatePdf()
    {
        $data = $this->getDoctrine()->getRepository(Transaction::class)->findAll();
    
        $html = $this->renderView('transactions/transactionPDF.html.twig', [
            'transactions' => $data
        ]);
    
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
    
        $pdfOutput = $dompdf->output();
    
        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }
    #[Route('/filtre', name: 'app_transactions_filtre', methods: ['GET'])]
    public function filtreSec(Request $request, TransactionRepository $transactionRepository): Response
    {
        $transactions = $transactionRepository->findAll();
    
        $filtered_data = array_filter($transactions, function($transaction) {
            if($transaction->getMontant() == "500"){
                 return $transaction->getMontant() == "500";
            }
           
        });
    
        // Pass the filtered data to the Twig template
        return $this->render('transactions/wireTransfer_Ad.html.twig', [
            'transactions' => $filtered_data,
        ]);
    }
    
     #[Route('/wire_Transfer_Admin', name: 'admin_transactions_index', methods: ['GET'])]
     public function alltransactionAdmin(TransactionRepository $transactionRepository): Response
     {
        return $this->render('transactions/wireTransfer_Ad.html.twig', [          
            'transactions' => $transactionRepository->findAll(),
        ]);
     }
    #[Route('/Money_Request_Admin', name: 'moneyRequest_Ad', methods: ['GET'])]
      public function ALLMoneyRequest(TransactionRepository $transactionRepository): Response
      {
          return $this->render('transactions/moneyRequest_Ad.html.twig', [
              'transactions' => $transactionRepository->findAll(),
          ]);
      }
  
      /******** Wire Transfer *********/
      #[Route('/wire_Transfer', name: 'app_transactions_index', methods: ['GET'])]
      public function index(TransactionRepository $transactionRepository): Response
      {
          return $this->render('transactions/index.html.twig', [
              'typeTransaction'=>"Wire Transfer",
              'transactions' => $transactionRepository->findAll(),
          ]);
      }
    
      /*****History  */
      #[Route('/transferHistory', name: 'transfer_history', methods: ['GET'])]
      public function history(TransactionRepository $transactionRepository): Response
      {
          return $this->render('transactions/transferHistory.html.twig', [          
              'transactions' => $transactionRepository->findAll(),
          ]);
      }
  
      #[Route('/new', name: 'app_transactions_new', methods: ['GET', 'POST'])]
      public function new(Request $request, TransactionRepository $transactionRepository, AgenceRepository $agenceRepository): Response
      {
          $agences = $agenceRepository->findAll();
          $choices = [];
          foreach ($agences as $agence) {
              $choices[$agence->getName()] = $agence->getName();
          }
          $transaction = new Transaction();
          $date = new \DateTime('now');
          $transaction->setDate($date);
          $transaction->setStatue('in progress');
          $transaction->setTypeTransaction("Wire Transfer");
  
          $form = $this->createForm(TransactionType::class, $transaction,[
              'choices' => $choices,
          ]);
          $form->handleRequest($request);
  
          if ($form->isSubmitted() && $form->isValid()) {
                 
  
              $transactionRepository->save($transaction, true);
  
              return $this->redirectToRoute('app_transactions_index', [], Response::HTTP_SEE_OTHER);
          }
  
          return $this->renderForm('transactions/new.html.twig', [
              'transaction' => $transaction,
              'form' => $form,
          ]);
      }
  
      /******* Send Money *******/
      #[Route('/send_money', name: 'send_transactions_sendMoney', methods: ['GET', 'POST'])]
      public function sendMony(Request $request, TransactionRepository $transactionRepository): Response
      {        
          $transaction = new Transaction();
          $date = new \DateTime('now');
          $transaction->setDate($date);        
          $transaction->setTypeTransaction("Send Money");
          $transaction->setAgenceName("no agence");
          $transaction->setStatue('valide');
          $form = $this->createForm(SendMoneyType::class, $transaction);
          $form->handleRequest($request);
  
          if ($form->isSubmitted() && $form->isValid()) {
              $transactionRepository->save($transaction, true);
              return $this->redirectToRoute('app_transactions_index', [], Response::HTTP_SEE_OTHER);
          }
  
          return $this->renderForm('transactions/sendMoney.html.twig', [
              'transaction' => $transaction,
              'form' => $form,
          ]);
      }
  
      #[Route('/{id}', name: 'app_transactions_show', methods: ['GET'])]
      public function show(Transaction $transaction): Response
      {
          return $this->render('transactions/show.html.twig', [
              'transaction' => $transaction,
          ]);
      }
  
      #[Route('/{id}/edit', name: 'app_transactions_edit', methods: ['GET', 'POST'])]
      public function edit(Request $request, Transaction $transaction, TransactionRepository $transactionRepository,AgenceRepository $agenceRepository): Response
      {
          $agences = $agenceRepository->findAll();
          $choices = [];
          foreach ($agences as $agence) {
              $choices[$agence->getNom()] = $agence->getNom();
          }
          $form = $this->createForm(TransactionType::class, $transaction,[
              'choices' => $choices,
          ]); 
          $form->handleRequest($request);
  
          if ($form->isSubmitted() && $form->isValid()) {
             
              $transactionRepository->save($transaction, true);
  
              return $this->redirectToRoute('app_transactions_index', [], Response::HTTP_SEE_OTHER);
          }
  
          return $this->renderForm('transactions/edit.html.twig', [
              'transaction' => $transaction,
              'form' => $form,
          ]);
      }
  
      #[Route('/{id}', name: 'app_transactions_delete', methods: ['POST'])]
      public function delete(Request $request, Transaction $transaction, TransactionRepository $transactionRepository): Response
      {
          if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
              $transactionRepository->remove($transaction, true);
          }
  
          return $this->redirectToRoute('app_transactions_index', [], Response::HTTP_SEE_OTHER);
      }
  
      #[Route('/accept/{id}', name: 'app_transactions_accept', methods: ['POST','GET'])]
      public function accept(Request $request, Transaction $transaction, TransactionRepository $transactionRepository): Response
      {
          if ($this->isCsrfTokenValid('accept'.$transaction->getId(), $request->request->get('_token'))) {
                $transaction->setStatue('valide');             
                $transactionRepository->save($transaction, true);
                $sid='AC1fbb5ea9ffd12d556edf30e576f7857e';
                $token='508891ca39dcc3e51ec0caebaaa3228a';
                $from = '+15076085816';
                $twilio = new Client($sid, $token); 
            
               $message = $twilio->messages 
                 ->create("+21621866975", // to 
                   array(  
                      "messagingServiceSid" => "MGb984d25fde1b017dff3bdae06c274ff9",      
                      "body" => " MAZEBANK-Your transaction has been accepted" 
                   ) 
               ); 
            }
  
          return $this->redirectToRoute('admin_transactions_index', [], Response::HTTP_SEE_OTHER);
      }
  
      #[Route('/reject/{id}', name: 'app_transactions_reject', methods: ['POST'])]
      public function reject(Request $request, Transaction $transaction, TransactionRepository $transactionRepository): Response
      {
          if ($this->isCsrfTokenValid('reject'.$transaction->getId(), $request->request->get('_token'))) {
                $transaction->setStatue('rejected');
                $transactionRepository->save($transaction, true);
                $sid='AC1fbb5ea9ffd12d556edf30e576f7857e';
                $token='508891ca39dcc3e51ec0caebaaa3228a';
                $from = '+15076085816';
                $twilio = new Client($sid, $token); 
            
               $message = $twilio->messages 
                 ->create("+21621866975", // to 
                   array(  
                      "messagingServiceSid" => "MGb984d25fde1b017dff3bdae06c274ff9",      
                      "body" => " MAZEBANK-Your transaction has been rejected" 
                   ) 
               ); 
  
          }
  
          return $this->redirectToRoute('admin_transactions_index', [], Response::HTTP_SEE_OTHER);
      }
  
      
    
         
  




}
