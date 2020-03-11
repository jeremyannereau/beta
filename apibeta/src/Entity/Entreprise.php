<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EntrepriseRepository")
 */
class Entreprise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"entreprise_candidature"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"entreprise_candidature"})
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Candidature", mappedBy="id_entreprise")
     */
    private $candidatures;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"entreprise_candidature"})
     */
    private $secteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"entreprise_candidature"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"entreprise_candidature"})
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contact", mappedBy="id_entreprise")
     */
    private $contacts;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"entreprise_candidature"})
     */
    private $departement;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"entreprise_candidature"})
     */
    private $ville;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Collection|Candidature[]
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures[] = $candidature;
            $candidature->setIdEntreprise($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->contains($candidature)) {
            $this->candidatures->removeElement($candidature);
            // set the owning side to null (unless already changed)
            if ($candidature->getIdEntreprise() === $this) {
                $candidature->setIdEntreprise(null);
            }
        }

        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(?string $secteur): self
    {
        $this->secteur = $secteur;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setIdEntreprise($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            // set the owning side to null (unless already changed)
            if ($contact->getIdEntreprise() === $this) {
                $contact->setIdEntreprise(null);
            }
        }

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(string $departement): self
    {
        $this->departement = $departement;

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
}
