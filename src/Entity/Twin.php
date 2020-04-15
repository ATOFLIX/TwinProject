<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TwinRepository")
 */
class Twin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Axe", mappedBy="twin")
     */
    private $m_axe;

    public function __construct()
    {
        $this->m_axe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Axe[]
     */
    public function getMAxe(): Collection
    {
        return $this->m_axe;
    }

    public function addMAxe(Axe $mAxe): self
    {
        if (!$this->m_axe->contains($mAxe)) {
            $this->m_axe[] = $mAxe;
            $mAxe->setTwin($this);
        }

        return $this;
    }

    public function removeMAxe(Axe $mAxe): self
    {
        if ($this->m_axe->contains($mAxe)) {
            $this->m_axe->removeElement($mAxe);
            // set the owning side to null (unless already changed)
            if ($mAxe->getTwin() === $this) {
                $mAxe->setTwin(null);
            }
        }

        return $this;
    }
}
