<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticketibm
 *
 * @ORM\Table(name="ticketsibm")
 * @ORM\Entity(repositoryClass=TicketIbmRepository::class)
 */
class TicketIbm
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="nIncident", type="string", length=20, nullable=false)
     * @ORM\GeneratedValue
     */
    private string $nIncident;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAffectation", type="datetime", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private \DateTime $dateAffectation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=true)
     */
    private ?\DateTime $dateCreation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private ?string $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="etatIncident", type="string", length=20, nullable=true)
     */
    private ?string $etatIncident;

    /**
     * @var string|null
     *
     * @ORM\Column(name="impact", type="string", length=20, nullable=true)
     */
    private ?string $impact;

    /**
     * @var string|null
     *
     * @ORM\Column(name="urgence", type="string", length=20, nullable=true)
     */
    private ?string $urgence;

    /**
     * @var int
     *
     * @ORM\Column(name="priorite", type="integer", nullable=true)
     */
    private int $priorite;

    /**
     * @var int
     *
     * @ORM\Column(name="nbRelances", type="integer", nullable=true)
     */
    private int $nbRelances;

    /**
     * @var string|null
     *
     * @ORM\Column(name="incidentAffectedAt", type="string", length=50, nullable=true)
     */
    private ?string $incidentAffectedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nTache", type="string", length=20, nullable=true)
     */
    private ?string $nTache;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tacheAffectedAt", type="string", length=50, nullable=true)
     */
    private ?string $tacheAffectedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sujetTache", type="text", length=16777215, nullable=true)
     */
    private ?string $sujetTache;

    /**
     * @var string|null
     *
     * @ORM\Column(name="detailsTache", type="text", length=0, nullable=true)
     */
    private ?string $detailsTache;

    /**
     * @var string|null
     *
     * @ORM\Column(name="typeEquipement", type="text", length=65535, nullable=true)
     */
    private ?string $typeEquipement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="typeLogiciel", type="text", length=65535, nullable=true)
     */
    private ?string $typeLogiciel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomEquipement", type="string", length=50, nullable=true)
     */
    private ?string $nomEquipement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codeMagasin", type="string", length=20, nullable=true)
     */
    private ?string $codeMagasin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomMagasin", type="string", length=50, nullable=true)
     */
    private ?string $nomMagasin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="typeMagasin", type="string", length=50, nullable=true)
     */
    private ?string $typeMagasin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateMaj", type="datetime", nullable=true)
     */
    private ?\DateTime $dateMaj;

    /**
     * @var string
     *
     * @ORM\Column(name="etatTicket", type="string", length=0, nullable=false, options={"default"="'NON_RESOLU'"})
     */
    private string $etatTicket = 'NON_RESOLU';

    public function __construct()
    {
        $this->dateAffectation = new \DateTime();
        $this->dateCreation = new \DateTime();
        $this->dateMaj = new \DateTime();
    }


    public function getNIncident(): ?string
    {
        return $this->nIncident;
    }

    public function getDateAffectation(): ?\DateTime
    {
        return $this->dateAffectation;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEtatIncident(): ?string
    {
        return $this->etatIncident;
    }

    public function setEtatIncident(string $etatIncident): self
    {
        $this->etatIncident = $etatIncident;

        return $this;
    }

    public function getImpact(): ?string
    {
        return $this->impact;
    }

    public function setImpact(string $impact): self
    {
        $this->impact = $impact;

        return $this;
    }

    public function getUrgence(): ?string
    {
        return $this->urgence;
    }

    public function setUrgence(string $urgence): self
    {
        $this->urgence = $urgence;

        return $this;
    }

    public function getPriorite(): ?int
    {
        return $this->priorite;
    }

    public function setPriorite(int $priorite): self
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getNbRelances(): ?int
    {
        return $this->nbRelances;
    }

    public function setNbRelances(int $nbRelances): self
    {
        $this->nbRelances = $nbRelances;

        return $this;
    }

    public function getIncidentAffectedAt(): ?string
    {
        return $this->incidentAffectedAt;
    }

    public function setIncidentAffectedAt(string $incidentAffectedAt): self
    {
        $this->incidentAffectedAt = $incidentAffectedAt;

        return $this;
    }

    public function getNTache(): ?string
    {
        return $this->nTache;
    }

    public function setNTache(string $nTache): self
    {
        $this->nTache = $nTache;

        return $this;
    }

    public function getTacheAffectedAt(): ?string
    {
        return $this->tacheAffectedAt;
    }

    public function setTacheAffectedAt(string $tacheAffectedAt): self
    {
        $this->tacheAffectedAt = $tacheAffectedAt;

        return $this;
    }

    public function getSujetTache(): ?string
    {
        return $this->sujetTache;
    }

    public function setSujetTache(string $sujetTache): self
    {
        $this->sujetTache = $sujetTache;

        return $this;
    }

    public function getDetailsTache(): ?string
    {
        return $this->detailsTache;
    }

    public function setDetailsTache(string $detailsTache): self
    {
        $this->detailsTache = $detailsTache;

        return $this;
    }

    public function getTypeEquipement(): ?string
    {
        return $this->typeEquipement;
    }

    public function setTypeEquipement(string $typeEquipement): self
    {
        $this->typeEquipement = $typeEquipement;

        return $this;
    }

    public function getTypeLogiciel(): ?string
    {
        return $this->typeLogiciel;
    }

    public function setTypeLogiciel(string $typeLogiciel): self
    {
        $this->typeLogiciel = $typeLogiciel;

        return $this;
    }

    public function getNomEquipement(): ?string
    {
        return $this->nomEquipement;
    }

    public function setNomEquipement(string $nomEquipement): self
    {
        $this->nomEquipement = $nomEquipement;

        return $this;
    }

    public function getCodeMagasin(): ?string
    {
        return $this->codeMagasin;
    }

    public function setCodeMagasin(string $codeMagasin): self
    {
        $this->codeMagasin = $codeMagasin;

        return $this;
    }

    public function getNomMagasin(): ?string
    {
        return $this->nomMagasin;
    }

    public function setNomMagasin(string $nomMagasin): self
    {
        $this->nomMagasin = $nomMagasin;

        return $this;
    }

    public function getTypeMagasin(): ?string
    {
        return $this->typeMagasin;
    }

    public function setTypeMagasin(?string $typeMagasin): self
    {
        $this->typeMagasin = $typeMagasin;

        return $this;
    }

    public function getDateMaj(): ?\DateTime
    {
        return $this->dateMaj;
    }

    public function setDateMaj(\DateTime $dateMaj): self
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }

    public function getEtatTicket(): ?string
    {
        return $this->etatTicket;
    }

    public function setEtatTicket(string $etatTicket): self
    {
        $this->etatTicket = $etatTicket;

        return $this;
    }


}
