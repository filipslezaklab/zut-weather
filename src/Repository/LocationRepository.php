<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 *
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findOneByCity(string $city, ?string $country = null): ?Location
    {
        $qb = $this->createQueryBuilder('l');
        $qb->where('LOWER(l.city) = LOWER(:city)')
            ->setParameter('city', $city);
        if ($country !== null) {
            $qb->andWhere('LOWER(l.country) = LOWER(:country)')
                ->setParameter('country', $country);
        }
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

//    /**
//     * @return Location[] Returns an array of Location objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Location
//    {
////        return $this->createQueryBuilder('l')
////            ->andWhere('l.exampleField = :val')
////            ->setParameter('val', $value)
////            ->getQuery()
////            ->getOneOrNullResult()
//        ;
//    }
}
