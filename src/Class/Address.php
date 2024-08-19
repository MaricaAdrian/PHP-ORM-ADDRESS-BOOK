<?php
declare(strict_types=1);
namespace Entity;

use Traits\Timestamp;

require_once 'src/Traits/Timestamp.php';

class Address {

    use Timestamp;

    private ?int $id = null;
    private ?string $name = null;
    private ?string $firstName = null;
    private ?string $email = null;
    private ?string $zipCode = null;
    private ?string $street = null;
    private ?City $city = null;

    public function __construct() {
        $this->initializeTimestamps();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city): void
    {
        $this->city = $city;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}