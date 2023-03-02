<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    private $patronicName;

    #[ORM\Column(type: 'date')]
    private $dateOfBirth;

    #[ORM\Column(type: 'string', length: 255)]
    private $placeOfBirth;

    #[ORM\Column(type: 'string', length: 255)]
    private $adress;

    #[ORM\Column(type: 'boolean')]
    private $floor; //пол

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isPensioner; //пол

    #[ORM\Column(type: 'string', length: 255)]
    private $phoneHome;

    #[ORM\Column(type: 'string', length: 255)]
    private $phoneMobile;

    #[ORM\Column(type: 'string', length: 255)]
    private $mail;

    #[ORM\Column(type: 'string', length: 255)]
    private $workingPlace;

    #[ORM\Column(type: 'string', length: 255)]
    private $position;

    #[ORM\ManyToOne(targetEntity: "City", inversedBy: "employee")]
    #[ORM\JoinColumn(name:"city")]
    private $city;

    #[ORM\ManyToOne(targetEntity: "Disability", inversedBy: "employee")]
    #[ORM\JoinColumn(name:"disability")]
    private $disability;

    #[ORM\ManyToOne(targetEntity: "Citizenship", inversedBy: "employee")]
    #[ORM\JoinColumn(name:"citizenship")]
    private $citizenship;

    #[ORM\ManyToOne(targetEntity: "FamilyStatus", inversedBy: "employee")]
    #[ORM\JoinColumn(name:"familyStatus")]
    private $familyStatus;

    #[ORM\ManyToOne(targetEntity: "Conscript", inversedBy: "employee")]
    #[ORM\JoinColumn(name:"conscript", nullable: true)]
    private $conscript;

    #[ORM\Column(type: 'integer')]
    private $income = 1000;

    #[ORM\Column(type: 'string', length: 2)]
    private $passportSeries;

    #[ORM\Column(type: 'string', length: 14)]
    private $passportNumber;

    #[ORM\Column(type: 'string', length: 255)]
    private $passportIssuedBy;

    #[ORM\Column(type: 'date', length: 255)]
    private $passportStartDate;

    #[ORM\ManyToOne(targetEntity: "City", inversedBy: "passport")]
    #[ORM\JoinColumn(name:"passportCity")]
    private $passportCity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->lastName . ' ' . $this->firstName . ' ' . $this->patronicName;
    }

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLast(string $last): self
    {
        $this->last = $last;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassportStartDate()
    {
        return $this->passportStartDate;
    }

    /**
     * @param mixed $passportStartDate
     */
    public function setPassportStartDate($passportStartDate): void
    {
        $this->passportStartDate = $passportStartDate;
    }

    /**
     * @return mixed
     */
    public function getPassportIssuedBy()
    {
        return $this->passportIssuedBy;
    }

    /**
     * @param mixed $passportIssuedBy
     */
    public function setPassportIssuedBy($passportIssuedBy): void
    {
        $this->passportIssuedBy = $passportIssuedBy;
    }

    /**
     * @return mixed
     */
    public function getPassportCity()
    {
        return $this->passportCity;
    }

    /**
     * @param mixed $passportCity
     */
    public function setPassportCity($passportCity): void
    {
        $this->passportCity = $passportCity;
    }

    /**
     * @return mixed
     */
    public function getPassportNumber()
    {
        return $this->passportNumber;
    }

    /**
     * @param mixed $passportNumber
     */
    public function setPassportNumber($passportNumber): void
    {
        $this->passportNumber = $passportNumber;
    }

    /**
     * @return mixed
     */
    public function getPassportSeries()
    {
        return $this->passportSeries;
    }

    /**
     * @param mixed $passportSeries
     */
    public function setPassportSeries($passportSeries): void
    {
        $this->passportSeries = $passportSeries;
    }

    /**
     * @return mixed
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * @param mixed $income
     */
    public function setIncome($income): void
    {
        $this->income = $income;
    }

    /**
     * @return mixed
     */
    public function getConscript()
    {
        return $this->conscript;
    }

    /**
     * @param mixed $conscript
     */
    public function setConscript($conscript): void
    {
        $this->conscript = $conscript;
    }

    /**
     * @return mixed
     */
    public function getFamilyStatus()
    {
        return $this->familyStatus;
    }

    /**
     * @param mixed $familyStatus
     */
    public function setFamilyStatus($familyStatus): void
    {
        $this->familyStatus = $familyStatus;
    }

    /**
     * @return mixed
     */
    public function getCitizenship()
    {
        return $this->citizenship;
    }

    /**
     * @param mixed $citizenship
     */
    public function setCitizenship($citizenship): void
    {
        $this->citizenship = $citizenship;
    }

    /**
     * @return mixed
     */
    public function getDisability()
    {
        return $this->disability;
    }

    /**
     * @param mixed $disability
     */
    public function setDisability($disability): void
    {
        $this->disability = $disability;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPatronicName()
    {
        return $this->patronicName;
    }

    /**
     * @param mixed $patronicName
     */
    public function setPatronicName($patronicName): void
    {
        $this->patronicName = $patronicName;
    }

    /**
     * @param mixed $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return mixed
     */
    public function getIsPensioner()
    {
        return $this->isPensioner;
    }

    /**
     * @param mixed $isPensioner
     */
    public function setIsPensioner($isPensioner): void
    {
        $this->isPensioner = $isPensioner;
    }

    /**
     * @return mixed
     */
    public function getPlaceOfBirth()
    {
        return $this->placeOfBirth;
    }

    /**
     * @param mixed $placeOfBirth
     */
    public function setPlaceOfBirth($placeOfBirth): void
    {
        $this->placeOfBirth = $placeOfBirth;
    }

    /**
     * @return mixed
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * @param mixed $adress
     */
    public function setAdress($adress): void
    {
        $this->adress = $adress;
    }

    /**
     * @return mixed
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * @param mixed $floor
     */
    public function setFloor($floor): void
    {
        $this->floor = $floor;
    }

    /**
     * @return mixed
     */
    public function getPhoneHome()
    {
        return $this->phoneHome;
    }

    /**
     * @param mixed $phoneHome
     */
    public function setPhoneHome($phoneHome): void
    {
        $this->phoneHome = $phoneHome;
    }

    /**
     * @return mixed
     */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }

    /**
     * @param mixed $phoneMobile
     */
    public function setPhoneMobile($phoneMobile): void
    {
        $this->phoneMobile = $phoneMobile;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getWorkingPlace()
    {
        return $this->workingPlace;
    }

    /**
     * @param mixed $workingPlace
     */
    public function setWorkingPlace($workingPlace): void
    {
        $this->workingPlace = $workingPlace;
    }
}
