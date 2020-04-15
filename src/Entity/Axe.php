<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AxeRepository")
 */
class Axe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $angle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Twin", inversedBy="m_axe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $twin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAngle(): ?int
    {
        return $this->angle;
    }

    public function setAngle(int $angle): self
    {
        $this->angle = $angle;

        return $this;
    }

    public function getTwin(): ?Twin
    {
        return $this->twin;
    }

    public function setTwin(?Twin $twin): self
    {
        $this->twin = $twin;

        return $this;
    }
}
