<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NoteRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *    message="Le nom ne peut pas être vide.")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Le nom doit contenir au moins {{ limit }} caractères.",
     *      maxMessage = "Le nom doit contenir moins de {{ limit }} caractères.")
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *    message="La description ne peut pas être vide.")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "La description doit contenir au moins {{ limit }} caractères.",
     *      maxMessage = "La description contenir moins de {{ limit }} caractères.")
     */
    private string $text;

    /**
     * @ORM\Column(type="date")
     * 
     */
    private DateTimeInterface $startDate;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $endDate;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="note", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setNote($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getNote() === $this) {
                $document->setNote(null);
            }
        }

        return $this;
    }
}
