<?php

namespace App\Repository;

use App\Entity\CarteBancaire;
use App\Entity\TypeCarte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarteBancaire>
 *
 * @method CarteBancaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarteBancaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarteBancaire[]    findAll()
 * @method CarteBancaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarteBancaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarteBancaire::class);
    }
    public function findByType($nom)
    {

        return $this->createQueryBuilder('p')
        ->leftJoin('p.idtypecarte', 'c')
        ->andWhere('c.id = :nom')
        ->setParameter('nom', $nom)
        ->getQuery()
        ->getResult();
        
    }

    public function getStat(): array
    {
            return $this->createQueryBuilder('carteBancaire')
            ->select('count(carteBancaire.identifier) as nbre,idtypecarte.nom')
            ->leftJoin('carteBancaire.idtypecarte', 'idtypecarte')
            ->groupBy("carteBancaire.idtypecarte")
            ->getQuery()
            ->execute();

    }
   
    
    // public function findAllTypes()
    // {
    //     $qb = $this->createQueryBuilder('c')
    //         ->select('t.nom')
    //         ->join('c.type', 't')
    //         ->groupBy('t.nom')
    //         ->getQuery();
        
    //     return $qb->getResult();
    // }
    public function findAllTypes()
        {
            $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                  'SELECT t.nom
                     FROM App\Entity\TypeCarte t'
                    );

                 return $query->getResult();
        }


    public function save(CarteBancaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CarteBancaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CarteBancaire[] Returns an array of CarteBancaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CarteBancaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



}
