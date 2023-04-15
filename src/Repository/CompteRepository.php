<?php

namespace App\Repository;

use App\Entity\Compte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Compte>
 *
 * @method Compte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compte[]    findAll()
 * @method Compte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compte::class);
    }

    public function save(Compte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Compte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search($searchTerm)
    {
        $qb = $this->createQueryBuilder('cd');

        if ($searchTerm) {
            $qb->where('cd.title LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%')
                ->orderBy('cd.id', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }
    ////filtre AJAX
    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT e
                 FROM App\Entity\Compte e
                 WHERE e.statue LIKE :str OR e.rib LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();
    }
    ////filtre ajax 
    public function findProd(string $search = null): array
    {
            return $this->createQueryBuilder('compte')
            ->andWhere('compte.statue LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$search.'%')
            ->getQuery()
            ->execute();
    }
    public function findByType($type)
    {
        return $this->createQueryBuilder('p')
        ->leftJoin('p.idType', 'c')
        ->andWhere('c.id = :type')
        ->setParameter('type', $type)
        ->getQuery()
        ->getResult();
        
    }


    public function getStat(): array
    {
            return $this->createQueryBuilder('compte')
            ->select('count(compte.id) as nbre,idType.type')
            ->leftJoin('compte.idType', 'idType')
            ->groupBy("compte.idType")
            ->getQuery()
            ->execute();

    }


//    /**
//     * @return Compte[] Returns an array of Compte objects
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

//    public function findOneBySomeField($value): ?Compte
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
