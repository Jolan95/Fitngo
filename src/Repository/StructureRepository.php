<?php

namespace App\Repository;

use App\Entity\Structure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Structure>
 *
 * @method Structure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Structure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Structure[]    findAll()
 * @method Structure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StructureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Structure::class);
    }

    public function add(Structure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Structure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    //essai
    public function findByFilters($filter, $id): array
    {
        $query = $this->createQueryBuilder('s')
        // ->andWhere('s.franchise = :id')
        // ->setParameter('id', $id)
        ->select('u', 's', "f")
        ->join("s.Franchise", "f")
        ->join("s.user_info", "u")
        ->andWhere('f.id = :id')
        ->setParameter('id', $id);
 
        if ($filter["filter"] != "" ){   
             $query
             ->setParameter('val', $filter["filter"])
             ->andWhere('s.isActive = :val');
         }
         if ($filter["query"] != ""){   
             $query
            ->andWhere("u.name LIKE :search")
            ->setParameter('search', "%{$filter["query"]}%");
 
         }
    
     
     return $query
     ->getQuery()
     ->getResult();
    }
    public function findByFilter($filter, $id): array
    {
        $query = $this->createQueryBuilder('s')
        // ->andWhere('s.franchise = :id')
        // ->setParameter('id', $id)
        ->select('u', 's', "f")
        ->join("s.Franchise", "f")
        ->join("s.user_info", "u")
        ->andWhere('f.id = :id')
        ->setParameter('id', $id);
 
        if ($filter["filter"] != "" ){   
             $query
             ->setParameter('val', $filter["filter"])
             ->andWhere('s.isActive = :val');
         }
         if ($filter["query"] != ""){   
             $query
            ->andWhere("u.name LIKE :search")
            ->setParameter('search', "%{$filter["query"]}%");
 
         }
    
     
     return $query
     ->getQuery()
     ->getResult();
    }
//    /**
//     * @return Structure[] Returns an array of Structure objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Structure
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
