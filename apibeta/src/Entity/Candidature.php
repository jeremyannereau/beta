<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidatureRepository")
 */
class Candidature
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $relation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="candidatures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_candidature;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reponse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moyen_candidature;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getDateCandidature(): ?\DateTimeInterface
    {
        return $this->date_candidature;
    }

    public function setDateCandidature(\DateTimeInterface $date_candidature): self
    {
        $this->date_candidature = $date_candidature;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getMoyenCandidature(): ?string
    {
        return $this->moyen_candidature;
    }

    public function setMoyenCandidature(?string $moyen_candidature): self
    {
        $this->moyen_candidature = $moyen_candidature;

        return $this;
    }
}
