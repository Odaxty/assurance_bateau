<?php

namespace App\Repository;

use App\Entity\Boat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;


/**
 * @extends ServiceEntityRepository<Boat>
 */
class BoatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly CacheInterface $cache) {
        parent::__construct($registry, Boat::class);
    }

    public function findLuxuryBoats(): array
    {
        return $this->createQueryBuilder('b')
        ->andWhere('b.purchasePrice > :price')
            ->setParameter('price', 1000000)
            ->orderBy('b.purchasePrice', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getBoatById($id): ?Boat {
        $key = 'boat_item_' . $id;
        $item = $this->cache->getItem($key);

        if(!$item->isHit()){
            $boat = $this->find($id);

            $item->set($boat);
            $this->cache->save($item);
        }

        return$item->get();
    }

    public function findByFirstnameWithCache(string $firstname): array
    {
        $ids = $this->createQueryBuilder('b')
            ->select('b.id')
            ->join('b.account', 'a')
            ->where('a.firstname = :firstname')
            ->setParameter('firstname', $firstname)
            ->getQuery()
            ->getScalarResult();

        $listfinal =[];

        foreach ($ids as $row) {
            $listfinal[] = $this->getBoatById($row['id']);
        }

        return $listfinal;
    }
    //    /**
    //     * @return Boat[] Returns an array of Boat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Boat
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
