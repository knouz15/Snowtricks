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
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]

// #[ORM\EntityListeners(['App\EntityListener\UserListener'])]
/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
// #[Vich\Uploadable]
#@Ignore()
class User implements UserInterface, PasswordAuthenticatedUserInterface ,\Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $username = '';

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email()]
    #[Assert\Length(min: 2, max: 180)]
    private ?string $email = '';

    #[ORM\Column(type: 'json')]
    #[Assert\NotNull()]
    private array $roles = [];

    //#[Assert\EqualTo(propertyPath : "password",message :  "Le mot de passe n'est pas identique.")]
    private ?string $plainPassword='password';

    #[ORM\Column(type: 'string')]
    //#[Assert\NotBlank()]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable', 
    options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\NotNull()]
    private \DateTimeImmutable $createdAt;

    /**
     * @Vich\UploadableField(mapping="user_images", fileNameProperty="avatar")
     * @var File
     */
    // #[Vich\UploadableField(mapping: 'user_images', fileNameProperty: 'avatar')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string')]
    private ?string $avatar = '';

    // #[ORM\Column(type: 'string', nullable: true)]
    // private ?string $avatar = null;
    
    
    
    // #[Assert\Image(maxSize : "500k",maxSizeMessage :"Votre avatar ne doit pas d??passer 500 ko", nullable:true)]
    // #[ORM\Column(type: 'string', length: 255)]
    // private $avatar;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Trick::class, orphanRemoval: true)]
    private $tricks;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $token;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $iagreeTerms='';
    

    // #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    // private $comments;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        // $this->comments = new ArrayCollection();
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

    public function setEmail(string $email): self
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

    public function setUsername(string $username): self
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
    // public function getComments(): Collection
    // {
    //     return $this->comments;
    // }

    // public function addComment(Comment $comment): self
    // {
    //     if (!$this->comments->contains($comment)) {
    //         $this->comments[] = $comment;
    //         $comment->setUser($this);
    //     }

    //     return $this;
    // }

    // public function removeComment(Comment $comment): self
    // {
    //     if ($this->comments->removeElement($comment)) {
    //         // set the owning side to null (unless already changed)
    //         if ($comment->getUser() === $this) {
    //             $comment->setUser(null);
    //         }
    //     }

    //     return $this;
    // }


     /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
    */ 
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->createdAt = new \DateTimeImmutable();//ou updatedAt
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }


    
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void //self
    {
        $this->avatar = $avatar;

        //return $this;
    }

        public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->avatar,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->avatar,
           
        ) = unserialize($serialized);
    }

    // public function isVerified(): bool
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

    public function getIagreeTerms(): ?string
    {
        return $this->iagreeTerms;
    }

    public function setIagreeTerms(?string $iagreeTerms): self
    {
        $this->iagreeTerms = $iagreeTerms;

        return $this;
    }
}
