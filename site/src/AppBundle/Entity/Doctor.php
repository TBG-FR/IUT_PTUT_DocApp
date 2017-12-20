<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Doctor extends User
{
    /**
     * @ORM\Column(name="phone", type="string", length=10)
     * @Assert\NotBlank()
     * @Assert\Length(min=10, max=10)
     */
    private $phone;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Speciality")
     */
    private $specialities;

    /**
     * @ORM\Column(name="address", type="string")
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @ORM\Column(name="city", type="string")
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @ORM\Column(name="zip", type="string", length=5)
     * @Assert\Length(min=5,max=5)
     */
    private $zip;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getSpecialities()
    {
        return $this->specialities;
    }

    /**
     * @param mixed $specialities
     */
    public function setSpecialities($specialities)
    {
        $this->specialities = $specialities;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    public function isDoctor()
    {
        return true;
    }
}