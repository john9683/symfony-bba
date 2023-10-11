<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`theme`")
 */
class Theme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $code;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isDefault;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $subtitle;

    /**
     * @ORM\OneToMany(targetEntity=Paragraph::class, mappedBy="theme")
     * @var Collection
     */
    private Collection $paragraph;

    /**
     * @ORM\Column(type="json", nullable=false)
     * @var
     */
    private $keyword = [];

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     * @return $this
     */
    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     * @return $this
     */
    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return array
     */
    public function getKeyword(): array
    {
        return $this->keyword;
    }

    /**
     * @param array $keyword
     * @return $this
     */
    public function setKeyword(array $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * @return Collection<int, Paragraph>
     */
    public function getParagraph(): Collection
    {
        return $this->paragraph;
    }

    /**
     * @param Paragraph $paragraph
     * @return $this
     */
    public function addParagraph(Paragraph $paragraph): self
    {
        if (!$this->paragraph->contains($paragraph)) {
            $this->paragraph[] = $paragraph;
            $paragraph->setTheme($this);
        }

        return $this;
    }

    /**
     * @param Paragraph $paragraph
     * @return $this
     */
    public function removeParagraph(Paragraph $paragraph): self
    {
        if ($this->paragraph->removeElement($paragraph)) {
            if ($paragraph->getTheme() === $this) {
                $paragraph->setTheme(null);
            }
        }

        return $this;
    }

    /**
     * @return array|null
     */
    public function getKeywordsJson(): ?array
    {
        return $this->keywordsJson;
    }

    /**
     * @param array|null $keywordsJson
     * @return $this
     */
    public function setKeywordsJson(?array $keywordsJson): self
    {
        $this->keywordsJson = $keywordsJson;

        return $this;
    }
}
