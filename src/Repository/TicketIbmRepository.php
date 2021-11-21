<?php

namespace App\Repository;

use App\Entity\TicketIbm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketIbm|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketIbm|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketIbm[]    findAll()
 * @method TicketIbm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketIbmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketIbm::class);
    }

    /**
     * @return array|null
     */
    public function findOneByLastUpdatedDate(): ?array
    {
        return $this->createQueryBuilder('i')
            ->select("CONCAT(i.nIncident, ' ', i.dateAffectation) as nTicket, DATE_FORMAT(MAX(i.dateMaj),'%d/%m/%Y') as maxDate")
            ->where("i.etatTicket = 'NON_RESOLU' OR i.etatTicket = 'RESOLU'")
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findRecurringTickets(string $state): ?array
    {
        return $this->createQueryBuilder('i')
            ->select("i, DATE_FORMAT(MAX(i.dateAffectation),'%d/%m/%Y %H:%i:%s') as maxDate, COUNT(i.nIncident) as nbIncident")
            ->where('i.etatTicket = :state')
            ->setParameter('state', $state)
            ->groupBy('i.nIncident')
            ->having('COUNT(i.nIncident) >= 1')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findRecurringTicketDetailsBy(string $nIncident): ?array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.nIncident = :nIncident')
            ->setParameter('nIncident', $nIncident)
            ->orderBy('i.dateAffectation', 'DESC')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findByDays(int $value): ?array
    {
        return $this->createQueryBuilder('i')
            ->select('i, DATE_DIFF(CURRENT_DATE(), MAX(i.dateAffectation)) as nbDays')
            ->where("i.etatTicket = 'NON_RESOLU'")
            ->groupBy('i.nIncident')
            ->having('nbDays = :value')
            ->setParameter('value', $value)
            ->andHaving('COUNT(i.nIncident) >= 1')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findByWeeks(int $value1, int $value2): ?array
    {
        return $this->createQueryBuilder('i')
            ->select('i, DATE_DIFF(CURRENT_DATE(), MAX(i.dateAffectation)) as nbDays')
            ->where("i.etatTicket = 'NON_RESOLU'")
            ->groupBy('i.nIncident')
            ->having('nbDays > :value1')
            ->andHaving('nbDays <= :value2')
            ->setParameter('value1', $value1)
            ->setParameter('value2', $value2)
            ->andHaving('COUNT(i.nIncident) >= 1')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findByMoreOneMonth(int $value): ?array
    {
        return $this->createQueryBuilder('i')
            ->select('i, DATE_DIFF(CURRENT_DATE(), MAX(i.dateAffectation)) as nbDays')
            ->where("i.etatTicket = 'NON_RESOLU'")
            ->groupBy('i.nIncident')
            ->having('nbDays > :value')
            ->setParameter('value', $value)
            ->andHaving('COUNT(i.nIncident) >= 1')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findByImpact(string $impact): ?array
    {
        return $this->createQueryBuilder('i')
            ->select('i.nIncident')
            ->where('i.impact = :impact')
            ->andWhere("i.etatTicket = 'NON_RESOLU'")
            ->setParameter('impact', $impact)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findByUrgence(string $urgence): ?array
    {
        return $this->createQueryBuilder('i')
            ->select('i.nIncident')
            ->where('i.urgence = :urgence')
            ->andWhere("i.etatTicket = 'NON_RESOLU'")
            ->setParameter('urgence', $urgence)
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /**
     * @return array|null
     */
    public function findByNoneResolvedOrResolved(string $etatTicket): ?array
    {
        return $this->createQueryBuilder('i')
            ->select("i.nIncident, i.dateAffectation")
            ->where("i.etatTicket = :etatTicket")
            ->setParameter('etatTicket', $etatTicket)
            ->getQuery()
            ->getScalarResult()
        ;
    }

    public function findOneByIncidentAndDateAffectation(string $nIncident, string $datetime): ?TicketIbm
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.nIncident = :nIncident')
            ->andWhere('i.dateAffectation = :datetime')
            ->setParameter('nIncident', $nIncident)
            ->setParameter('datetime', $datetime)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}