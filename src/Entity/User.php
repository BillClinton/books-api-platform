<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ApiResource(
 *     security="is_granted('ROLE_USER')",
 *     collectionOperations={
 *          "get",
 *          "post"={
 *            "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *            "validation_groups"={"Default", "create"}
 *     },
 *     itemOperations={
 *          "get",
 *          "put"={"security"="is_granted('ROLE_USER') and object == user"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 * )
 * @ApiFilter(PropertyFilter::class)
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
  /**
   * @ORM\Id()
   * @Groups({"user:read"})
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=180, unique=true)
   * @Groups({"user:read", "user:write"})
   * @Assert\NotBlank()
   */
  private $username;

  /**
   * @ORM\Column(type="json")
   */
  private $roles = [];

  /**
   * @var string The hashed password
   * @ORM\Column(type="string")
   * @Groups({"user:write"})
   */
  private $password;

  /**
   * @Groups("user:write")
   * @SerializedName("password")
   * @Assert\NotBlank(groups={"create"})
   */
  private $plainPassword;

  /**
   * @ORM\Column(type="string", length=255, unique=true)
   * @Groups({"user:read", "user:write"})
   * @Assert\NotBlank()
   * @Assert\Email()
   */
  private $email;

  /**
   * @ApiSubresource()
   * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="owner", orphanRemoval=true)
   */
  private $books;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\Author", mappedBy="owner", orphanRemoval=true)
   */
  private $authors;

  public function __construct()
  {
    $this->books = new ArrayCollection();
    $this->authors = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUsername(): string
  {
    return (string) $this->username;
  }

  public function setUsername(string $username): self
  {
    $this->username = $username;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function getPassword(): string
  {
    return (string) $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function getSalt()
  {
    // not needed when using the "bcrypt" algorithm in security.yaml
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials()
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

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
      $book->setOwner($this);
    }

    return $this;
  }

  public function removeBook(Book $book): self
  {
    if ($this->books->contains($book)) {
      $this->books->removeElement($book);
      // set the owning side to null (unless already changed)
      if ($book->getOwner() === $this) {
        $book->setOwner(null);
      }
    }

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
      $author->setOwner($this);
    }

    return $this;
  }

  public function removeAuthor(Author $author): self
  {
    if ($this->authors->contains($author)) {
      $this->authors->removeElement($author);
      // set the owning side to null (unless already changed)
      if ($author->getOwner() === $this) {
        $author->setOwner(null);
      }
    }

    return $this;
  }

  public function getPlainPassword(): ?string
  {
    return $this->plainPassword;
  }

  public function setPlainPassword(string $plainPassword): self
  {
    $this->plainPassword = $plainPassword;
    return $this;
  }
}
