<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function userDisabled($years, int $limit = null)
    {
        return $this->createQueryBuilder('u')
            ->where('u.updatedAt <= :date_end')
            ->andWhere('u.isActive = :is_active')
            ->setParameter('date_end', new \DateTime('-'.$years.' years'))
            ->setParameter('is_active', true)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countUserDisabled($years)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.updatedAt <= :date_end')
            ->andWhere('u.isActive = :is_active')
            ->setParameter('date_end', new \DateTime('-'.$years.' years'))
            ->setParameter('is_active', true)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
