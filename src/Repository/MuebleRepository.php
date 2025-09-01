<?php

namespace App\Repository;

use App\Entity\Mueble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mueble>
 */
class MuebleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mueble::class);
    }

    /**
     * Buscar muebles por nombre (búsqueda parcial)
     * @param string $nombre
     * @return Mueble[]
     */
    public function findByNombre(string $nombre): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.nombre LIKE :nombre')
            ->setParameter('nombre', '%' . $nombre . '%')
            ->orderBy('m.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Mueble[] Returns an array of Mueble objects
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

    //    public function findOneBySomeField($value): ?Mueble
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
