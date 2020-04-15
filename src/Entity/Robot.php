<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="robot")
 * @ORM\Entity(repositoryClass="App\Repository\RobotRepository")
 * @UniqueEntity(
 * fields = {"ip"},
 * message = "L'adresse ip que vous avez saisi est déjà utilisé !")
 */
class Robot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Ip
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modele;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     * @Assert\LessThanOrEqual(10)
     */
    private $axes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $this->modele = $modele;

        return $this;
    }

    public function getAxes()
    {
        return $this->axes;
    }

    public function setAxes($axes): self
    {
        $this->axes = $axes;

        return $this;
    }
}
