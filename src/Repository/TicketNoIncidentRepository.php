<?php

namespace App\Repository;

use App\Entity\TicketNoIncident;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketNoIncident|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketNoIncident|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketNoIncident[]    findAll()
 * @method TicketNoIncident[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketNoIncidentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketNoIncident::class);
    }

      /**
     * @return TicketNoIncident[] Returns an array of TicketNoIncident objects
     */
    public function findByTicketState(string $state): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.etat = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?TicketNoIncident
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
