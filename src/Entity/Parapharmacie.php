<?php

namespace App\Entity;

use App\Repository\ParapharmacieRepository;
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParapharmacieRepository::class)]
class Parapharmacie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'adresse ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $adresse = null;

    #[ORM\Column(length: 8, nullable: true)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: '/^\d{8}$/',
        message: "Le numéro de téléphone doit comporter exactement 8 chiffres."
    )]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
