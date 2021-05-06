<?php

namespace App\Entity;

use App\Repository\TestQuestionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TestQuestionsRepository::class)
 */
class TestQuestions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $test_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $variations;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTestId(): ?int
    {
        return $this->test_id;
    }

    public function setTestId(int $test_id): self
    {
        $this->test_id = $test_id;

        return $this;
    }

    public function getVariations(): ?string
    {
        return $this->variations;
    }

    public function setVariations(?string $variations): self
    {
        $this->variations = $variations;

        return $this;
    }
}
