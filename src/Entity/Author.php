<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *    itemOperations={
 *      "get"={
 *        "normalization_context"={"groups"={"author:read", "author:item:get"}},
 *      },
 *      "put"={
 *        "security"="is_granted('EDIT', object)",
 *        "security_message"="Only the creator can edit an author"
 *      },
 *      "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *    },
 *    collectionOperations={
 *      "get",
 *      "post"={"security"="is_granted('ROLE_USER')"}
 *    },
 *    normalizationContext={"groups"={"author:read"}},
 *    denormalizationContext={"groups"={"author:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @ORM\EntityListeners({"App\Doctrine\AuthorSetOwnerListener"})
 */
class Author
{
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   * @Groups({"author:read", "book:read" })
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   * @Groups({"author:read", "author:write", "book:read", "book:write"})
   */
  private $name;

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Book", mappedBy="authors")
   */
  private $books;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="authors")
   * @ORM\JoinColumn(nullable=false)
   * @Groups({"author:read", "author:collection:post"})
   */
  private $owner;

  public function __construct()
  {
    $this->books = new ArrayCollection();
  }

  /**
   * @Groups({"author:read", "book:read" })
   */
  public function getIri(): string
  {
    return "/api/author/" . $this->getId();
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

  /**
   * @return Collection|Book[]
   */
  public function getBooks(): Collection
  {
    return $this->books;
  }

  public function addBook(Book $book): self
  {
    if (!$this->books->contains($book)) {
      $this->books[] = $book;
      $book->addAuthor($this);
    }

    return $this;
  }

  public function removeBook(Book $book): self
  {
    if ($this->books->contains($book)) {
      $this->books->removeElement($book);
      $book->removeAuthor($this);
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
