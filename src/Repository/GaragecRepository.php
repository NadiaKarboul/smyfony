<?php

namespace App\Repository;

use App\Entity\Garagec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Garagec>
 *
 * @method Garagec|null find($id, $lockMode = null, $lockVersion = null)
 * @method Garagec|null findOneBy(array $criteria, array $orderBy = null)
 * @method Garagec[]    findAll()
 * @method Garagec[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GaragecRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garagec::class);
    }

    public function save(Garagec $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Garagec $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function orderById() :array {
        return $this->createQueryBuilder('g')
                ->orderBy('g.id','DESC')
                ->getQuery()
                ->getResult();
    }
//    /**
//     * @return Garagec[] Returns an array of Garagec objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Garagec
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
