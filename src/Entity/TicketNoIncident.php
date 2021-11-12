<?php

namespace App\Entity;

use App\Repository\TicketNoIncidentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * TicketNoIncident
 *
 * @ORM\Table(name="ticketsnoincident")
 * @ORM\Entity(repositoryClass=TicketNoIncidentRepository::class)
 */
class TicketNoIncident
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private int $id;

    /**
     * @ORM\Column(name="nomMagasin", type="string", length=100, nullable=true)
     */
    private ?string $nomMagasin;

    /**
     * @ORM\Column(name="codeMagasin", type="string", length=20, nullable=true)
     */
    private ?string $codeMagasin;

    /**
     * @ORM\Column(name="ville", type="string", length=100, nullable=true)
     */
    private ?string $ville;

    /**
     * @ORM\Column(name="codeGeode", type="string", length=20, nullable=true)
     */
    private ?string $codeGeode;

    /**
     * @ORM\Column(name="nKit", type="string", length=20, nullable=true)
     */
    private ?string $nKit;

    /**
     * @ORM\Column(name="dateInstall", type="datetime", nullable=true)
     */
    private ?\DateTime $dateInstall;

    /**
     * @ORM\Column(name="commentaire", type="text", length=0, nullable=true)
     */
    private ?string $commentaire;

    /**
     * @ORM\Column(name="arsRecup", type="string", length=50, nullable=true)
     */
    private ?string $arsRecup;

    /**
     * @ORM\Column(name="etat", type="string", length=0, nullable=false, options={"default"="'traité'"})
     */
    private string $etat = 'traité';

    /**
     * @ORM\Column(name="dateArchive", type="datetime", nullable="true")
     */
    private ?\DateTime $dateArchive;

    /**
     * @ORM\Column(name="dateMaj", type="datetime", nullable="true")
     */
    private ?\DateTime $dateMaj;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCodeMagasin(): ?string
    {
        return $this->codeMagasin;
    }

    public function setCodeMagasin(string $codeMagasin): self
    {
        $this->codeMagasin = $codeMagasin;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodeGeode(): ?string
    {
        return $this->codeGeode;
    }

    public function setCodeGeode(string $codeGeode): self
    {
        $this->codeGeode = $codeGeode;

        return $this;
    }

    public function getNKit(): ?string
    {
        return $this->nKit;
    }

    public function setNKit(string $nKit): self
    {
        $this->nKit = $nKit;

        return $this;
    }

    public function getDateInstall(): ?\DateTime
    {
        return $this->dateInstall;
    }

    public function setDateInstall(?\DateTime $dateInstall): self
    {
        $this->dateInstall = $dateInstall;

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

    public function getArsRecup(): ?string
    {
        return $this->arsRecup;
    }

    public function setArsRecup(string $arsRecup): self
    {
        $this->arsRecup = $arsRecup;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

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

    public function getDateMaj(): ?\DateTime
    {
        return $this->dateMaj;
    }

    public function setDateMaj(\DateTime $dateMaj): self
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }
}
