<?php

namespace App\Repository;

use App\Entity\Maintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Maintenance>
 *
 * @method Maintenance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Maintenance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Maintenance[]    findAll()
 * @method Maintenance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maintenance::class);
    }

    public function save(Maintenance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Maintenance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findMaintenanceByIdUser($id)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("SELECT m FROM App\Entity\Maintenance m WHERE m.user = :id  ORDER BY m.id DESC ");
        $query->setParameter('id', $id);
        return $query->getResult();
    }
    public function orderById() :array {
        return $this->createQueryBuilder('m')
                ->orderBy('m.id','DESC')
                ->getQuery()
                ->getResult();
    }
    public function findLastMaintenanceByIdUser($id)
{
    $entityManager = $this->getEntityManager();
    $query = $entityManager->createQuery("SELECT m FROM App\Entity\Maintenance m WHERE m.user = :id ORDER BY m.id DESC");
    $query->setParameter('id', $id);
    $query->setMaxResults(1); // limiter à une seule entité
    return $query->getResult();
}
//    /**
//     * @return Maintenance[] Returns an array of Maintenance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   public function findOneBySomeField($value): ?Maintenance
   {
       return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
          ->setParameter('val', $value)
         ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
