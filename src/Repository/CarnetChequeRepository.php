<?php

namespace App\Repository;

use App\Entity\CarnetCheque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarnetCheque>
 *
 * @method CarnetCheque|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarnetCheque|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarnetCheque[]    findAll()
 * @method CarnetCheque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarnetChequeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarnetCheque::class);
    }

    public function save(CarnetCheque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CarnetCheque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getStat(): array
    {
            return $this->createQueryBuilder('carnetcheque')
            ->select('count(carnetcheque.identifier) as nbre,idtypecarnet.nom')
            ->leftJoin('carnetcheque.idtypecarnet', 'idtypecarnet')
            ->groupBy("carnetcheque.idtypecarnet")
            ->getQuery()
            ->execute();

    }


//    /**
//     * @return CarnetCheque[] Returns an array of CarnetCheque objects
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

//    public function findOneBySomeField($value): ?CarnetCheque
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
