<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ApiResource(
    operations: [new GetCollection(), new Post()],
    order: ['nom' => 'ASC']
)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('post:collection')] // TODO rendre non modifiable via apiplatform
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 60)]
    #[Assert\Regex('/^[ 0-9A-Za-zÀ-ÖØ-öø-ÿ\'-]+$/')]
    #[Groups(['liste_lieux_par_ville'])]
    private ?string $nom = null;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 5)]
    #[Assert\Regex('/^[0-9]{5}$/')]
    #[Groups(['liste_lieux_par_ville', 'post:collection'])]
    private ?string $codePostal = null;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Lieu::class, orphanRemoval: true)]
    private Collection $lieus;

    public function __construct()
    {
        $this->lieus = new ArrayCollection();
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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieus(): Collection
    {
        return $this->lieus;
    }

    public function addLieu(Lieu $lieu): self
    {
        if (!$this->lieus->contains($lieu)) {
            $this->lieus->add($lieu);
            $lieu->setVille($this);
        }

        return $this;
    }

    public function removeLieu(Lieu $lieu): self
    {
        if ($this->lieus->removeElement($lieu)) {
            // set the owning side to null (unless already changed)
            if ($lieu->getVille() === $this) {
                $lieu->setVille(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNom().' - '.$this->getCodePostal();
    }
}
