<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Speciality
 *
 * @ORM\Table(name="speciality")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpecialityRepository")
 * @UniqueEntity(fields={"name"})
 */
class Speciality
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\Doctor", mappedBy="specialities")
     */
    private $doctors;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Appointment", mappedBy="specialities")
     */
    private $appointments;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDoctors()
    {
        return $this->doctors;
    }

    /**
     * @param mixed $doctors
     */
    public function setDoctors($doctors)
    {
        $this->doctors = $doctors;
    }

    /**
     * @return mixed
     */
    public function getAppointments()
    {
        return $this->appointments;
    }

    /**
     * @param mixed $appointments
     */
    public function setAppointments($appointments)
    {
        $this->appointments = $appointments;
    }

}