<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(nullable: true)]
    private ?float $vat = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'author_id', targetEntity: UserLog::class)]
    private Collection $userLogsAuthor;

    #[ORM\OneToMany(mappedBy: 'target_id', targetEntity: UserLog::class)]
    private Collection $userLogsTarget;

    #[ORM\OneToMany(mappedBy: 'customer_id', targetEntity: Invoice::class)]
    private Collection $invoicesId;

    public function __construct()
    {
        $this->userLogsAuthor = new ArrayCollection();
        $this->userLogsTarget = new ArrayCollection();
        $this->invoicesId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getVat(): ?float
    {
        return $this->vat;
    }

    public function setVat(?float $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, UserLog>
     */
    public function getUserLogsAuthor(): Collection
    {
        return $this->userLogsAuthor;
    }

    public function addUserLogsAuthor(UserLog $userLogsAuthor): self
    {
        if (!$this->userLogsAuthor->contains($userLogsAuthor)) {
            $this->userLogsAuthor->add($userLogsAuthor);
            $userLogsAuthor->setAuthorId($this);
        }

        return $this;
    }

    public function removeUserLogsAuthor(UserLog $userLogsAuthor): self
    {
        if ($this->userLogsAuthor->removeElement($userLogsAuthor)) {
            // set the owning side to null (unless already changed)
            if ($userLogsAuthor->getAuthorId() === $this) {
                $userLogsAuthor->setAuthorId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserLog>
     */
    public function getUserLogsTarget(): Collection
    {
        return $this->userLogsTarget;
    }

    public function addUserLogsTarget(UserLog $userLogsTarget): self
    {
        if (!$this->userLogsTarget->contains($userLogsTarget)) {
            $this->userLogsTarget->add($userLogsTarget);
            $userLogsTarget->setTargetId($this);
        }

        return $this;
    }

    public function removeUserLogsTarget(UserLog $userLogsTarget): self
    {
        if ($this->userLogsTarget->removeElement($userLogsTarget)) {
            // set the owning side to null (unless already changed)
            if ($userLogsTarget->getTargetId() === $this) {
                $userLogsTarget->setTargetId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoicesId(): Collection
    {
        return $this->invoicesId;
    }

    public function addInvoicesId(Invoice $invoicesId): self
    {
        if (!$this->invoicesId->contains($invoicesId)) {
            $this->invoicesId->add($invoicesId);
            $invoicesId->setCustomerId($this);
        }

        return $this;
    }

    public function removeInvoicesId(Invoice $invoicesId): self
    {
        if ($this->invoicesId->removeElement($invoicesId)) {
            // set the owning side to null (unless already changed)
            if ($invoicesId->getCustomerId() === $this) {
                $invoicesId->setCustomerId(null);
            }
        }

        return $this;
    }
}
