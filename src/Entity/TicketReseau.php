<?php

namespace App\Entity;

use App\Repository\TicketReseauRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * TicketReseau
 *
 * @ORM\Table(name="ticketsreseau")
 * @ORM\Entity(repositoryClass=TicketReseauRepository::class)
 */
class TicketReseau
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="nTicket", type="string", length=20, nullable=false)
     */
    private string $nTicket;

    /**
     * @var string
     *
     * @ORM\Column(name="codeIncident", type="string", length=20, nullable=false)
     */
    private string $codeIncident;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=true)
     */
    private ?\DateTime $dateCreation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codeMagasin", type="string", length=10, nullable=true)
     */
    private ?string $codeMagasin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomMagasin", type="string", length=250, nullable=true)
     */
    private ?string $nomMagasin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="typeMagasin", type="string", length=100, nullable=true)
     */
    private ?string $typeMagasin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codeMaintneur", type="string", length=100, nullable=true)
     */
    private ?string $codeMaintneur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private ?string $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="historique", type="text", length=0, nullable=true)
     */
    private ?string $historique;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateMaj", type="datetime", nullable=true)
     */
    private ?\DateTime $dateMaj;

    /**
     * @var string
     *
     * @ORM\Column(name="etatTicket", type="string", length=0, nullable=false, options={"default"="'Ticket_ouvert'"})
     */
    private string $etatTicket = 'Ticket_ouvert';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateInstall", type="datetime", nullable=true)
     */
    private ?\DateTime $dateInstall;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateArchive", type="datetime", nullable=true)
     */
    private ?\DateTime $dateArchive;

    /**
     * @var string|null
     *
     * @ORM\Column(name="arsInstall", type="string", length=50, nullable=true)
     */
    private ?string $arsInstall;

    /**
     * @var string|null
     *
     * @ORM\Column(name="arsRecup", type="string", length=50, nullable=true)
     */
    private ?string $arsRecup;

    /**
     * @ORM\Column(name="commentaire", type="text", length=0, nullable=true)
     */
    private $commentaire;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->dateInstall = new \DateTime();
        $this->dateArchive = new \DateTime();
        $this->dateMaj = new \DateTime();
    }

    public function getNTicket(): ?string
    {
        return $this->nTicket;
    }

    public function setNTicket(?string $nTicket): self
    {
        $this->nTicket = $nTicket;

        return $this;
    }

    public function getCodeIncident(): ?string
    {
        return $this->codeIncident;
    }

    public function setCodeIncident(string $codeIncident): self
    {
        $this->codeIncident = $codeIncident;

        return $this;
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

    public function setTypeMagasin(string $typeMagasin): self
    {
        $this->typeMagasin = $typeMagasin;

        return $this;
    }

    public function getCodeMaintneur(): ?string
    {
        return $this->codeMaintneur;
    }

    public function setCodeMaintneur(string $codeMaintneur): self
    {
        $this->codeMaintneur = $codeMaintneur;

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

    public function getHistorique(): ?string
    {
        return $this->historique;
    }

    public function setHistorique(string $historique): self
    {
        $this->historique = $historique;

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

    public function getDateInstall(): ?\DateTime
    {
        return $this->dateInstall;
    }

    public function setDateInstall(\DateTime $dateInstall): self
    {
        $this->dateInstall = $dateInstall;

        return $this;
    }

    public function getDateArchive(): ?\DateTime
    {
        return $this->dateArchive;
    }

    public function setDateArchive(\DateTime $dateArchive): self
    {
        $this->dateArchive = $dateArchive;

        return $this;
    }

    public function getArsInstall(): ?string
    {
        return $this->arsInstall;
    }

    public function setArsInstall(string $arsInstall): self
    {
        $this->arsInstall = $arsInstall;

        return $this;
    }

    public function getArsRecup(): ?string
    {
        return $this->arsRecup;
    }

    public function setArsRecup(string $arsRecup): self
    {
        $this->arsRecup = $arsRecup;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }


}
