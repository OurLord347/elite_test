<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TestRepository::class)
 */
class Test
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $child = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $parent_test_id;

    public function getParentTestId(): ?string
    {
        return $this->parent_test_id;
    }

    public function setParentTestId(string $parent_test_id): self
    {
        $this->parent_test_id = $parent_test_id;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChild(): ?bool
    {
        return $this->child;
    }

    public function setChild(bool $child): self
    {
        $this->child = $child;

        return $this;
    }
}
