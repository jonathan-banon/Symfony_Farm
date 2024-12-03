<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Animal>
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    
    public function findByTypeId(int $typeId): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.type', 't')
            ->andWhere('t.id = :typeId') 
            ->setParameter('typeId', $typeId)
            ->getQuery()
            ->getResult(); 
    }
}
