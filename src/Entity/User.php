<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $type = null;

    #[ORM\Column(length: 60)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(nullable: true)]
    private ?float $vat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: UserLog::class)]
    private Collection $userLogsAuthor;

    #[ORM\OneToMany(mappedBy: 'target', targetEntity: UserLog::class)]
    private Collection $userLogsTarget;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Invoice::class)]
    private Collection $userInvoices;

    public function __construct()
    {
        $this->userLogsAuthor = new ArrayCollection();
        $this->userLogsTarget = new ArrayCollection();
        $this->userInvoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
            $userLogsAuthor->setAuthor($this);
        }

        return $this;
    }

    public function removeUserLogsAuthor(UserLog $userLogsAuthor): self
    {
        if ($this->userLogsAuthor->removeElement($userLogsAuthor)) {
            // set the owning side to null (unless already changed)
            if ($userLogsAuthor->getAuthor() === $this) {
                $userLogsAuthor->setAuthor(null);
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
            $userLogsTarget->setTarget($this);
        }

        return $this;
    }

    public function removeUserLogsTarget(UserLog $userLogsTarget): self
    {
        if ($this->userLogsTarget->removeElement($userLogsTarget)) {
            // set the owning side to null (unless already changed)
            if ($userLogsTarget->getTarget() === $this) {
                $userLogsTarget->setTarget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getUserInvoices(): Collection
    {
        return $this->userInvoices;
    }

    public function addUserInvoice(Invoice $userInvoice): self
    {
        if (!$this->userInvoices->contains($userInvoice)) {
            $this->userInvoices->add($userInvoice);
            $userInvoice->setCustomer($this);
        }

        return $this;
    }

    public function removeUserInvoice(Invoice $userInvoice): self
    {
        if ($this->userInvoices->removeElement($userInvoice)) {
            // set the owning side to null (unless already changed)
            if ($userInvoice->getCustomer() === $this) {
                $userInvoice->setCustomer(null);
            }
        }

        return $this;
    }
}
