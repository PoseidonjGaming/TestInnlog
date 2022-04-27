<?php

namespace App\Entity;

use App\Repository\ParcourRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParcourRepository::class)
 */
class Parcour
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="parcours")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=TypeSortie::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $TypeSortie;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $heureDebut;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $distance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTypeSortie(): ?TypeSortie
    {
        return $this->TypeSortie;
    }

    public function setTypeSortie(?TypeSortie $TypeSortie): self
    {
        $this->TypeSortie = $TypeSortie;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

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

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function dataJson(){      
        $data=[
            'id'=>$this->getId(),
            'user'=>$this->getUser()->getUsername(),
            'Sortie'=>$this->getTypeSortie()->getLibelle(),
            "Duree"=>$this->getDuree(),
            "commentaire"=>$this->getCommentaire(),
            'heureDebut'=>$this->getHeureDebut(),
            'distance'=>$this->getDistance()
        ];
        return $data;
    }
}
