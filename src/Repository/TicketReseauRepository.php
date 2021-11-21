<?php

namespace App\Repository;

use App\Entity\TicketReseau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketReseau|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketReseau|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketReseau[]    findAll()
 * @method TicketReseau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketReseauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketReseau::class);
    }

    /**
     * @return array
     */
    public function findOneByLastUpdatedDate(): array
    {
        return $this->createQueryBuilder('k')
            ->select("k.nTicket, DATE_FORMAT(MAX(k.dateMaj),'%d/%m/%Y') as maxDate")
            ->where("k.etatTicket = 'Ticket_ouvert'")
            ->orWhere("k.etatTicket = 'Ticket_traité'")
            ->orWhere("k.etatTicket = 'Ticket_a_fermer'")
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return TicketReseau[] Returns an array of TicketReseau objects
     */
    public function findByTicketState(string $state): array
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.etatTicket = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array
     */
    public function findRecurringStores(): array
    {
        return $this->createQueryBuilder('k')
            ->select('k, COUNT(k.nTicket) as nbTicket')
            ->where('CURRENT_DATE() - k.dateCreation <= 365')
            ->groupBy('k.codeMagasin')
            ->orderBy('nbTicket', 'DESC')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    public function findRecurringStoreDetailsBy(string $codeMagasin): array
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.codeMagasin = :codeMagasin')
            ->setParameter('codeMagasin', $codeMagasin)
            ->orderBy('k.dateCreation', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByOpenedTreatedAndToClose(): array
    { 
        return $this->createQueryBuilder('k')
            ->select("k.nTicket, k.etatTicket, k.dateCreation, k.dateInstall, k.dateArchive")
            ->where("k.etatTicket = 'Ticket_ouvert'")
            ->orWhere("k.etatTicket = 'Ticket_traité'")
            ->orWhere("k.etatTicket = 'Ticket_a_fermer'")
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?TicketReseau
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}