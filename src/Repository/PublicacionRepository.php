<?php

namespace App\Repository;

use App\Entity\Publicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicacion[]    findAll()
 * @method Publicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicacion::class);
    }

    public function getPublicacionesByUser($user)
    {
        $user_id = $user ? $user->getId() : '0';
        $publicaciones = $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->where('u.id = :user_id')->setParameter('user_id', $user_id)
            ->orderBy('p.created', 'desc')
            ->getQuery()
            ->getResult();

        return $publicaciones;
    }

    public function getAllHastags()
    {
        $publicaciones = $this->createQueryBuilder('p')->orderBy('p.created', 'desc')->getQuery()->getResult();
        $hastags_existentes = [];
        foreach ($publicaciones as $publicacion) {
            $hastags = $publicacion->getHastags();
            if ($hastags) {
                foreach ($hastags as $hastag) {
                    if (!in_array($hastag, $hastags_existentes)) {
                        $hastags_existentes[] = $hastag;
                    }
                }
            }
        }
        return $hastags_existentes;
    }

    public function getPublicacionesByHastag($hastag) {
        $qb = $this->createQueryBuilder('p')
            ->where('p.hastags LIKE :hastag')
            ->setParameter('hastag', '%'.$hastag.'%')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getPublicacionesPublicas()
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->where('u.isPublic = true');
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Publicacion[] Returns an array of Publicacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Publicacion
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
