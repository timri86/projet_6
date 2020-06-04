<?php

namespace App\Repository;

use App\Entity\Tricks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Tricks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tricks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tricks[]    findAll()
 * @method Tricks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TricksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tricks::class);
    }

    public  function showOneTrick($id){

        $qb=$this->createQueryBuilder('t')
            ->where('t.id=:id')
            ->setParameter('id',$id)
            ->leftJoin('t.image','i')
            ->addSelect('i')
            ->leftJoin('t.video','v')
            ->addSelect('v')
            ->leftJoin('t.user', 'u')
            ->addSelect('u')
            ->leftJoin('t.comments', 'c')
            ->addSelect('c')
            ->getQuery();

        return $qb->getResult();
    }
}
