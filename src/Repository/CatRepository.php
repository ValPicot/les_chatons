<?php

namespace App\Repository;

use App\Entity\Cat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Cat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cat[]    findAll()
 * @method Cat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cat::class);
    }

    public function findVisibleCats(User $user): Query
    {
        if ($user->hasRoles(User::ROLE_ADMIN)) {
            return $this->createQueryBuilder('c')
                ->orderBy('c.id')
                ->getQuery();
        }

        return $this->createQueryBuilder('c')
            ->orderBy('c.id')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery();
    }
}
