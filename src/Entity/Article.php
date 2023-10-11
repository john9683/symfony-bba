<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private string $trialArticleId;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @var User
     */
    private User $author;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private string $body;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $theme;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $title;

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    private array $keyword = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     * @var array|null
     */
    private ?array $words = [];

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @var array|null
     */
    private ?array $size = [];

    /**
     * @ORM\OneToOne(targetEntity=Images::class, cascade={"persist", "remove"})
     * @var Images|null
     */
    private ?Images $images;

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
    public function getTrialArticleId(): ?string
    {
        return $this->trialArticleId;
    }

    /**
     * @param string|null $trialArticleId
     * @return $this
     */
    public function setTrialArticleId(?string $trialArticleId): self
    {
        $this->trialArticleId = $trialArticleId;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     * @return $this
     */
    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

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
     * @return array|null
     */
    public function getKeyword(): ?array
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
     * @return array|null
     */
    public function getWords(): ?array
    {
        return $this->words;
    }

    /**
     * @param array|null $words
     * @return $this
     */
    public function setWords(?array $words): self
    {
        $this->words = $words;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable|null $createdAt
     * @return $this
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSize(): ?array
    {
        return $this->size;
    }

    /**
     * @param array|null $size
     * @return $this
     */
    public function setSize(?array $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Images|null
     */
    public function getImages(): ?Images
    {
        return $this->images;
    }

    /**
     * @param Images|null $images
     * @return $this
     */
    public function setImages(?Images $images): self
    {
        $this->images = $images;

        return $this;
    }
}
