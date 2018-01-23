<?php

namespace UserBundle\Entity;

use AppBundle\Entity\Address;
use AppBundle\Entity\Office;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="UserBundle\Repository\DoctorRepository")
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Address", cascade={"persist","remove"})
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Office", mappedBy="doctor", cascade={"persist", "remove"})
     */
    private $offices;

    public function __construct()
    {
        parent::__construct();
        $this->offices = new ArrayCollection();
    }

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
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return ArrayCollection
     */
    public function getOffices()
    {
        return $this->offices;
    }

    /**
     * @param Office $office
     */
    public function addOffice($office)
    {
        $this->offices->add($office);
    }

    /**
     * @param Office $office
     */
    public function removeOffice($office)
    {
        $this->offices->remove($office);
    }

    public function isDoctor()
    {
        return true;
    }
}