<?php

namespace App\Entity;

use App\Repository\TypeDepositRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDepositRepository::class)]
class TypeCredit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $percent;

    #[ORM\OneToMany(targetEntity: "Account", mappedBy: "typeDeposit")]
    #[ORM\JoinColumn(nullable: true)]
    private $account;


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

    public function getPercent(): ?int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsReturnable()
    {
        return $this->isReturnable;
    }

    /**
     * @param mixed $isReturnable
     */
    public function setIsReturnable($isReturnable): void
    {
        $this->isReturnable = $isReturnable;
    }
}
