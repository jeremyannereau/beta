<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormationUsersRepository")
 */
class FormationUsers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Formation", inversedBy="formationUsers")
     */
    private $idFormation;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="formationUsers")
     */
    private $idUser;

    public function __construct()
    {
        $this->idFormation = new ArrayCollection();
        $this->idUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getIdFormation(): Collection
    {
        return $this->idFormation;
    }

    public function addIdFormation(Formation $idFormation): self
    {
        if (!$this->idFormation->contains($idFormation)) {
            $this->idFormation[] = $idFormation;
        }

        return $this;
    }

    public function removeIdFormation(Formation $idFormation): self
    {
        if ($this->idFormation->contains($idFormation)) {
            $this->idFormation->removeElement($idFormation);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getIdUser(): Collection
    {
        return $this->idUser;
    }

    public function addIdUser(User $idUser): self
    {
        if (!$this->idUser->contains($idUser)) {
            $this->idUser[] = $idUser;
        }

        return $this;
    }

    public function removeIdUser(User $idUser): self
    {
        if ($this->idUser->contains($idUser)) {
            $this->idUser->removeElement($idUser);
        }

        return $this;
    }
}
