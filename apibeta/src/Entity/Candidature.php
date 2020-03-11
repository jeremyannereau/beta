<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidatureRepository")
 */
class Candidature
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"nomansland"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="candidatures")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"nomansland"})
     */
    private $id_user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="candidatures")
     * @Groups({"nomansland"})
     */
    private $id_entreprise;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"nomansland"})
     */
    private $date_candidature;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"nomansland"})
     */
    private $reponse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"nomansland"})
     */
    private $moyen_candidature;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdEntreprise(): ?Entreprise
    {
        return $this->id_entreprise;
    }

    public function setIdEntreprise(?Entreprise $id_entreprise): self
    {
        $this->id_entreprise = $id_entreprise;

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
