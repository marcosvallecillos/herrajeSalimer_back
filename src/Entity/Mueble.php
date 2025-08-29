<?php

namespace App\Entity;

use App\Repository\MuebleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: MuebleRepository::class)]
class Mueble
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $num_pieces = null;

    /**
     * @var Collection<int, Herrajes>
     */
    #[ORM\OneToMany(targetEntity: Herrajes::class, mappedBy: 'mueble_id')]
    private Collection $herrajes;

    public function __construct()
    {
        $this->herrajes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getNumPieces(): ?int
    {
        return $this->num_pieces;
    }

    public function setNumPieces(int $num_pieces): static
    {
        $this->num_pieces = $num_pieces;

        return $this;
    }

    /**
     * @return Collection<int, Herrajes>
     */
    public function getHerrajes(): Collection
    {
        return $this->herrajes;
    }

    public function addHerraje(Herrajes $herraje): static
    {
        if (!$this->herrajes->contains($herraje)) {
            $this->herrajes->add($herraje);
            $herraje->setMuebleId($this);
        }

        return $this;
    }

    public function removeHerraje(Herrajes $herraje): static
    {
        if ($this->herrajes->removeElement($herraje)) {
            // set the owning side to null (unless already changed)
            if ($herraje->getMuebleId() === $this) {
                $herraje->setMuebleId(null);
            }
        }

        return $this;
    }
}
