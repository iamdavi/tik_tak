<?php

namespace App\Repository;

use App\Entity\Likes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    public function getLikeByUserAndPublicacion($user, $publicacion)
    {
        $user_id = $user ? $user->getId() : '0';
        $publicacion_id = $publicacion ? $publicacion->getId() : '0';
        $qb = $this->createQueryBuilder('l')
            ->innerJoin('l.user', 'u')
            ->innerJoin('l.publicacion', 'p')
            ->where('l.user = :user_id')->setParameter('user_id', $user_id)
            ->andWhere('l.publicacion = :publicacion_id')->setParameter('publicacion_id', $publicacion_id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $qb;
    }

}
