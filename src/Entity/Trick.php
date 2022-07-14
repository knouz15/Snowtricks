<?php

namespace App\Entity;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[UniqueEntity(fields: ['name'], message: 'Un trick à ce nom existe déjà sur le site')]
// #[Vich\Uploadable]
#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $slug;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'datetime_immutable', 
    options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    // #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'trick')]
    // #[ORM\JoinColumn(nullable: false)]
    // private $user;

    
    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist','remove'])]
    private $images;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Tag::class, orphanRemoval: true, cascade: ['persist','remove'])]
    private $tags;

//    #[ ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class, orphanRemoval: true)]
//     private $comments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        // $this->updatedAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
        // $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        // $this->slug = $name;
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    // #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps()
    {
        // if($this->getCreatedAt() === null){
        // $this->setCreatedAt = new \DateTimeImmutable();
        $this->setUpdatedAt = new \DateTimeImmutable();
      
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    //pour aller chercher les images d'un trick
    public function getImages(): Collection
    {
        return $this->images;
    }

    //pour ajouter une image au trick
    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setTrick($this);
            //$this->setUpdatedAt(new \DateTimeImmutable);
        }

        return $this;
    }

    //pour supprimer les images d'un trick
    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setTrick($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag) ;
                // set the owning side to null (unless already changed)
                if ($tag->getTrick() === $this) {
                    $tag->setTrick(null);
                }
            }

        return $this;
    }

    // /**
    //  * @return Collection<int, Comment>
    //  */
    // public function getComments(): Collection
    // {
    //     return $this->comments;
    // }

    // public function addComment(Comment $comment): self
    // {
    //     if (!$this->comments->contains($comment)) {
    //         $this->comments[] = $comment;
    //         $comment->setTrick($this);
    //     }

    //     return $this;
    // }

    // public function removeComment(Comment $comment): self
    // {
    //     if ($this->comments->removeElement($comment)) {
    //         // set the owning side to null (unless already changed)
    //         if ($comment->getTrick() === $this) {
    //             $comment->setTrick(null);
    //         }
    //     }

    //     return $this;
    // }

    public function __toString()
    {
        return $this->slug;
    }
}
