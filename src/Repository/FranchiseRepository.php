<?php

namespace App\Repository;

use App\Entity\Franchise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
USE App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends ServiceEntityRepository<Franchise>
 *
 * @method Franchise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Franchise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Franchise[]    findAll()
 * @method Franchise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FranchiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Franchise::class);
    }

    public function add(Franchise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Franchise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Franchise[] Returns an array of Franchise objects
//     */

    public function findByFilters($filter, $search): array
    {
        $query = $this->createQueryBuilder('f')
        ->select('u', 'f')
        ->join("f.user_info", "u");
        
        if ($filter != null ){   
            $query
            ->setParameter('val', $filter)
            ->andWhere('f.isActive = :val');
        }
        if ($search != ""){   
            $query
           ->andWhere("u.name LIKE :search")
           ->setParameter('search', "{$search}%");
    
        }
        // if ($filter["query"] != ""){   
            //     $query
            //    ->andWhere("u.name LIKE :search")
            //    ->setParameter('search', "%{$filter["query"]}%");

        // }
   
    
    return $query
    ->getQuery()
    ->getResult();
   }

//    SELECT * 
//    FROM franchise
//    JOIN user ON franchise.id = franchise_id
//    WHERE franchise.is_active = 1 AND user.name LIKE '%Bor%'
//    public function findOneBySomeField($value): ?Franchise
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
