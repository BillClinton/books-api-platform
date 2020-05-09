<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *    itemOperations={
 *      "get"={
 *        "normalization_context"={"groups"={"book:read", "book:item:get"}},
 *      },
 *      "put"={
 *        "security"="is_granted('EDIT', object)",
 *        "security_message"="Only the creator can edit an book"
 *      },
 *      "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *    },
 *    collectionOperations={
 *      "get",
 *      "post"={"security"="is_granted('ROLE_USER')"}
 *    },
 *    normalizationContext={"groups"={"book:read"}},
 *    denormalizationContext={"groups"={"book:write"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *     "owner": "exact"
 * })
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\EntityListeners({"App\Doctrine\BookSetOwnerListener"})
 */
class Book
{
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   * @Groups({"book:read"})
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   * @Groups({"book:read", "book:write"})
   * @Assert\NotBlank()
   */
  private $name;

  /**
   * @ORM\Column(type="string", length=40, nullable=true)
   */
  private $isbn;

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Author", inversedBy="books", cascade={"persist"})
   * @Groups({"book:read", "book:write"})
   * @Assert\Valid()
   */
  private $authors;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="books")
   * @ORM\JoinColumn(nullable=false)
   * @Groups({"book:read", "book:write"})
   */
  private $owner;

  public function __construct()
  {
    $this->authors = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getIsbn(): ?string
  {
    return $this->isbn;
  }

  public function setIsbn(?string $isbn): self
  {
    $this->isbn = $isbn;

    return $this;
  }

  /**
   * @return Collection|Author[]
   */
  public function getAuthors(): Collection
  {
    return $this->authors;
  }

  public function addAuthor(Author $author): self
  {
    if (!$this->authors->contains($author)) {
      $this->authors[] = $author;
    }

    return $this;
  }

  public function removeAuthor(Author $author): self
  {
    if ($this->authors->contains($author)) {
      $this->authors->removeElement($author);
    }

    return $this;
  }

  public function getOwner(): ?User
  {
    return $this->owner;
  }

  public function setOwner(?User $owner): self
  {
    $this->owner = $owner;

    return $this;
  }
}
