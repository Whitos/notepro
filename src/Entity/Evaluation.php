<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column]
    private ?int $bareme = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professor $professor = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassLevel $classLevel = null;

    #[ORM\OneToMany(mappedBy: 'evaluation', targetEntity: Grade::class, orphanRemoval: true)]
    private Collection $grades;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getBareme(): ?int
    {
        return $this->bareme;
    }

    public function setBareme(int $bareme): static
    {
        $this->bareme = $bareme;

        return $this;
    }

    public function getProfessor(): ?Professor
    {
        return $this->professor;
    }

    public function setProfessor(?Professor $professor): static
    {
        $this->professor = $professor;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getClassLevel(): ?ClassLevel
    {
        return $this->classLevel;
    }

    public function setClassLevel(?ClassLevel $classLevel): static
    {
        $this->classLevel = $classLevel;

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setEvaluation($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getEvaluation() === $this) {
                $grade->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getGradeByStudent(Student $student): ?Grade
    {
        foreach ($this->getGrades() as $grade){
            if($grade->getStudent() === $student){
                return $grade;
            }
        }
        return null;
    }

    public function getAverageGradeClasse(): ?float
    {
        $nbNotes = $this->grades->count();     // variable $nbNotes qui va recuperer une collection de evaluations et compter le nombre de notes
        if ($nbNotes === 0) { // si le nombre de notes est égale à 0 retourne null
            return null;
        }
        $sum = 0; // variable $sum qui va recuperer la somme des notes
        foreach ($this->grades as $grade) {    //pour chaque evaluation dans la collection d'evaluations on va recuperer la note et l'ajouter à la somme
            $sum += $grade->getGrade();
        }
        return $sum / $nbNotes;         // retourne la moyenne des notes   // PS cest mon code
    }
}
