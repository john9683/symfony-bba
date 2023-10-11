<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    private array $filesNames = [];

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array|null
     */
    public function getFilesNames(): ?array
    {
        return $this->filesNames;
    }

    /**
     * @param array $filesNames
     * @return $this
     */
    public function setFilesNames(array $filesNames): self
    {
        $this->filesNames = $filesNames;

        return $this;
    }
}
