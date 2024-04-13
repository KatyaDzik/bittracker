<?php

namespace App\Entity;

use App\Repository\TorrentFileRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TorrentFileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TorrentFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'torrents')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private ?User $author = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $file;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $status;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'torrents')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private ?Category $category = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();

        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updated_at = new DateTimeImmutable();

        return $this;
    }
}