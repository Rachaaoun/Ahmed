<?php

namespace App\Entity;

use App\Repository\HebergementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Utils;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=HebergementRepository::class)
 */
class Hebergement
{
    /**
     * @Groups("hebergement")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("hebergement")
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 6,
     *      minMessage = "votre nom doit etre compose d'au moins  {{ limit }} caractere",
     *      maxMessage = "Your  name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $nom;

    /**
     * @Groups("hebergement")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="il faut saisir votre adresse")
     */
    private $adresse;

    /**
     * @Groups("hebergement")
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(message="Veillerz indiquer le type de sejour  ")
     */
    private $type;

    /**
     * @Groups("hebergement")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="il faut mettre le nombre de chambre ")
     */
    private $nbrChambre;

    /**
     * @Groups("hebergement")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="veillez indiquer le type de chambre ")
     */
    private $typeChambre;

    /**
     * @Groups("hebergement")
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="hebergement")
     */
    private $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNbrChambre(): ?string
    {
        return $this->nbrChambre;
    }

    public function setNbrChambre(string $nbrChambre): self
    {
        $this->nbrChambre = $nbrChambre;

        return $this;
    }

    public function getTypeChambre(): ?string
    {
        return $this->typeChambre;
    }

    public function setTypeChambre(string $typeChambre): self
    {
        $this->typeChambre = $typeChambre;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setHebergement($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getHebergement() === $this) {
                $reservation->setHebergement(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (String)$this->getId();

    }


}
