<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'index_client')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
       /***Transaction */
        $query = $entityManager->createQuery('SELECT t FROM App\Entity\Transaction t');
        $transactions = $query->getResult();


        $nbSM=0;

        foreach ($transactions as $t) {
            if ($t->getTypeTransaction() == "Send Money") {
                $nbSM++;
            }
        }
        $nbV=0;

        foreach ($transactions as $t) {
           if ($t->getTypeTransaction()=="Wire Transfer"){
              $nbV++;
           }
        }
       /****Account** */

        $query = $entityManager->createQuery('SELECT a FROM App\Entity\Compte a');
        $comptes = $query->getResult();

        return $this->render('home/user_dashboard.html.twig', [
            'transactionCount' =>count($transactions),
            'sendMoneyCount'  => $nbSM,
            'wireTransferCount'   =>$nbV,
            'accountCount'  =>count($comptes),
        ]);

    }
    #[Route('/statistique', name: 'static_client')]
    public function statistique(EntityManagerInterface $entityManager): Response
    {
         return $this->render('home/user_dashboard.html.twig', [
         
        ]);

    }
   
    #[Route('/statis', name: 'stat')]
    public function statistiques2(TransactionRepository $transactionRepository)
    {
        $commande = $transactionRepository->countByDate();
        $dates = [];
        $commandeCount = [];
        foreach ($commande as $com) {
            $dates[] = $com['date']->format('Y-m-d');
            $commandeCount[] = $com['count'];
        }
    
        return $this->render('client/user_dashboard.html.twig', [
            'dates' => json_encode($dates),
            'commandeCount' => json_encode($commandeCount),
        ]);
    }

}
