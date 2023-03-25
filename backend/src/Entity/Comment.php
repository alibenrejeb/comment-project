<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[groups(['get_comment', 'get_comment_reply', 'update_comment'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[groups(['get_comment', 'get_comment_reply', 'update_comment'])]
    #[Assert\NotBlank(groups: ['create_comment'])]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[groups(['get_comment'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[groups(['get_comment'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[groups(['get_comment'])]
    private ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: CommentReply::class, orphanRemoval: true)]
    #[groups(['get_comment'])]
    private Collection $commentReplies;

    #[ORM\Column]
    private ?int $pageId = null;

    #[ORM\Column]
    #[groups(['get_comment', 'get_comment_reply', 'update_comment'])]
    private ?int $rating = null;

    public function __construct()
    {
        $this->commentReplies = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        if (empty($this->updatedAt)) {
            $this->updatedAt = new \DateTime();
        }

        if (empty($this->rating)) {
            $this->rating = 0;
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
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

    /**
     * @return Collection<int, CommentReply>
     */
    public function getCommentReplies(): Collection
    {
        return $this->commentReplies;
    }

    public function addCommentReply(CommentReply $commentReply): self
    {
        if (!$this->commentReplies->contains($commentReply)) {
            $this->commentReplies->add($commentReply);
            $commentReply->setComment($this);
        }

        return $this;
    }

    public function removeCommentReply(CommentReply $commentReply): self
    {
        if ($this->commentReplies->removeElement($commentReply)) {
            // set the owning side to null (unless already changed)
            if ($commentReply->getComment() === $this) {
                $commentReply->setComment(null);
            }
        }

        return $this;
    }

    public function getPageId(): ?int
    {
        return $this->pageId;
    }

    public function setPageId(int $pageId): self
    {
        $this->pageId = $pageId;

        return $this;
    }
}
