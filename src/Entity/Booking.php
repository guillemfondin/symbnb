<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention la date doit être au bon format")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être supérieur à la date d'aujourd'hui", groups={"front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention la date doit être au bon format")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être inférieur à la date d'arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Comment", mappedBy="booking", cascade={"persist", "remove"})
     */
    private $avis;

    /**
     * Renseigne automatiquement la date de création et le prix total
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prePersist() {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        if (empty($this->amount)) {
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function isBookableDate() {
        // Les jours indispos
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // Les jours de la résa
        $bookingDays = $this->getDays();
        // Fonction format date
        $formatDay = function($day) {
            return $day->format('T-m-d');
        };
        // Array de string des jours de la résa
        $days = array_map($formatDay, $bookingDays);
        // Idem pour les jours indispo
        $notAvailable = array_map($formatDay, $notAvailableDays);
        // Puis on boucle sur chaque jour de la résa, et on vérifie qu'il est dispo
        foreach ($days as $day) {
            if (array_search($day, $notAvailable) !== false) return false;
        }
        return true;
    }

    /**
     * Récupère un tab des jourées de la résa
     *
     * @return Array Tableau d'objets DateTime représentant les jours d'occupation
     */
    public function getDays() {
        $resultat = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24*60*60
        );

        $days = array_map(function($dayTimestamp) {
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $resultat);

        return $days;

    }

    public function getDuration() {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAvis(): ?Comment
    {
        return $this->avis;
    }

    public function setAvis(?Comment $avis): self
    {
        $this->avis = $avis;

        // set (or unset) the owning side of the relation if necessary
        $newBooking = $avis === null ? null : $this;
        if ($newBooking !== $avis->getBooking()) {
            $avis->setBooking($newBooking);
        }

        return $this;
    }
}
