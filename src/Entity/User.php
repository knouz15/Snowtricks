<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte avec cet email existe déjà!')]
#[UniqueEntity(fields: ['username'], message: 'Un compte avec ce username existe déjà!')]

// #[ORM\EntityListeners(['App\EntityListener\UserListener'])]
/**
 * @ORM\Entity
 */
#@Ignore()
class User implements UserInterface, PasswordAuthenticatedUserInterface ,\Serializable
{ 
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 50)]
    
    private ?string $username;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    
    private ?string $email;

    #[ORM\Column(type: 'json')]
    
    private array $roles = [];
    // private array $roles = ['ROLE_USER'];

    
    //#[Assert\EqualTo(propertyPath : "password",message :  "Le mot de passe n'est pas identique.")]
    private ?string $plainPassword='password';

    #[ORM\Column(type: 'string')]
    //#[Assert\NotBlank()]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable', 
    options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'string', nullable: true)]
    private $avatarFilename;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Trick::class, orphanRemoval: true)]
    private $tricks;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $token;

    #[ORM\Column(type: 'boolean')]
    private $isVerified;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $iagreeTerms;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private $comments;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $resetToken;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;


    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->isVerified = false;
    }   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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
    * @return void
    *
    *  Get the value of plainPassword
    */   
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

  /**
   * @param [type] $plainPassword
   *
   * 
   * @return void
   */
     
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

  /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    /**
     * @return Collection<int, Trick>
     */
    public function getTrick(): Collection
    {
        return $this->trick;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->trick->contains($trick)) {
            $this->trick[] = $trick;
            $trick->setUser($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->trick->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getUser() === $this) {
                $trick->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    //  * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     
    // public function setImageFile(?File $imageFile = null): void
    // {
    //     $this->imageFile = $imageFile;

    //     if (null !== $imageFile) {
    //         // It is required that at least one field changes if you are using doctrine
    //         // otherwise the event listeners won't be called and the file is lost
    //         $this->createdAt = new \DateTimeImmutable();//ou updatedAt
    //     }
    // }

    // public function getImageFile(): ?File
    // {
    //     return $this->imageFile;
    // }
 

    
    public function getAvatarFilename(): ?string
    {
        return $this->avatarFilename;
    }

    public function setAvatarFilename(string $avatarFilename): self //void
    {
        $this->avatarFilename = $avatarFilename;

        return $this;
    }

        public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->avatarFilename,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->avatarFilename,
           
        ) = unserialize($serialized);
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getIagreeTerms(): ?\DateTime
    {
        return $this->iagreeTerms;
    }

    public function setIagreeTerms(?\DateTime $iagreeTerms): self
    {
        $this->iagreeTerms = $iagreeTerms;

        return $this;
    }

    // public function getPhoto(): ?string
    // {
    //     return $this->photo;
    // }

    // public function setPhoto(?string $photo): self
    // {
    //     $this->photo = $photo;

    //     return $this;
    // }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
