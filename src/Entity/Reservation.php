<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @Groups("reservation")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("reservation")
     * @ORM\Column(type="date")
     */
    private $date;
    /**
     * @Groups("reservation")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="il faut saisir votre nom")
     * @Assert\Length(
     *      min = 2,
     *      max = 6,
     *      minMessage = "votre nom doit etre compose d'au moins  {{ limit }} caractere ",
     *      maxMessage = "Your  name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $nom;

    /**
     * @Groups("reservation")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="il faut saisir votre prenom")
     */
    private $prenom;

    /**
     * @Groups("reservation")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $listHotel;

    /**
     * @Groups("reservation")
     * @ORM\Column(type="string", length=255)
     */
    private $prix;

    /**
     * @Groups("reservation")
     * @ORM\ManyToOne(targetEntity=Hebergement::class, inversedBy="reservations")
     */
    private $hebergement;
    /**
     * @CaptchaAssert\ValidCaptcha(
     *      message = "CAPTCHA validation failed, try again."
     * )
     */
    protected $captchaCode;

    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getListHotel(): ?string
    {
        return $this->listHotel;
    }

    public function setListHotel(?string $listHotel): self
    {
        $this->listHotel = $listHotel;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getHebergement(): ?Hebergement
    {

        return $this->hebergement;
    }

    public function setHebergement(?Hebergement $hebergement): self
    {
        $this->hebergement = $hebergement;

        return $this;
    }
}
