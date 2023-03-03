<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $number;

    #[ORM\ManyToOne(targetEntity: "Employee", inversedBy: "account")]
    #[ORM\JoinColumn(name:"employee")]
    private $employee;

    #[ORM\ManyToOne(targetEntity: "Currency", inversedBy: "account")]
    #[ORM\JoinColumn(name:"currency")]
    private $currency;

    #[ORM\ManyToOne(targetEntity: "TypeDeposit", inversedBy: "account")]
    #[ORM\JoinColumn(name:"type_deposit")]
    private $typeDeposit;

    #[ORM\ManyToOne(targetEntity: "typeCredit", inversedBy: "account")]
    #[ORM\JoinColumn(name:"type_credit")]
    private $typeCredit;

    #[ORM\Column(type: 'bigint')]
    private int $count = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $numberDeclaration;

    #[ORM\Column(type: 'date', nullable: true)]
    private $startDateDeposit;

    #[ORM\Column(type: 'date', nullable: true)]
    private $endDateDeposit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isMainAccount;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $countPercent = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountPercent(): int
    {
        return $this->countPercent=== null ? 0 : $this->countPercent;
    }

    public function setCountPercent(int $countPercent): self
    {
        $this->countPercent = $countPercent;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getCurrency() : Currency
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getTypeDeposit() : ?TypeDeposit
    {
        return $this->typeDeposit;
    }

    /**
     * @param mixed $typeDeposit
     */
    public function setTypeDeposit($typeDeposit): void
    {
        $this->typeDeposit = $typeDeposit;
    }

    /**
     * @return mixed
     */
    public function getStartDateDeposit() : ?\DateTime
    {
        return $this->startDateDeposit;
    }

    /**
     * @param mixed $startDateDeposit
     */
    public function setStartDateDeposit($startDateDeposit): void
    {
        $this->startDateDeposit = $startDateDeposit;
    }

    /**
     * @return mixed
     */
    public function getEndDateDeposit() : ?\DateTime
    {
        return $this->endDateDeposit;
    }

    /**
     * @param mixed $endDateDeposit
     */
    public function setEndDateDeposit($endDateDeposit): void
    {
        $this->endDateDeposit = $endDateDeposit;
    }

    /**
     * @return mixed
     */
    public function getIsMainAccount()
    {
        return $this->isMainAccount;
    }

    /**
     * @param mixed $isMainAccount
     */
    public function setIsMainAccount($isMainAccount): void
    {
        $this->isMainAccount = $isMainAccount;
    }

    /**
     * @return mixed
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param mixed $employee
     */
    public function setEmployee($employee): void
    {
        $this->employee = $employee;
    }
}
