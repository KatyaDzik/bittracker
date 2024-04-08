<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $name = '';

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true)]
    private ?Category $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class)]
    private ?Collection $children;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: TorrentFile::class)]
    private ?Collection $torrents;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->torrents = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category|null $parent
     * @return $this
     */
    public function setParent(?Category $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    /**
     * @param Collection|null $children
     * @return $this
     */
    public function setChildren(?Collection $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return Collection|TorrentFile[]
     */
    public function getTorrents(): ?Collection
    {
        return $this->torrents;
    }

    /**
     * @param Collection|null $torrents
     * @return $this
     */
    public function setTorrents(?Collection $torrents): self
    {
        $this->torrents = $torrents;

        return  $this;
    }
}