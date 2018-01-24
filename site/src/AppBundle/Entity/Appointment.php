<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Appointment
 *
 * @ORM\Table(name="appointment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppointmentRepository")
 */
class Appointment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(name="startTime", type="time")
     */
    private $startTime;

    /**
     * @ORM\Column(name="endTime", type="time")
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office", inversedBy="appointments")
     */
    private $office;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Speciality", inversedBy="appointments")
     */
    private $specialities;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="appointments")
     */
    private $user;

    /**
     * @ORM\Column(name="closed", type="boolean")
     */
    private $closed;

    /**
     * @ORM\Column(name="summary", type="text")
     */
    private $summary;

    private $distanceToUser = 0;

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    public function __construct()
    {
        $this->setDate(new \DateTime());
        $this->setStartTime(new \DateTime());
        $this->setEndTime(new \DateTime());
    }

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

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
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }
	
    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
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
     * @return Appointment
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param Appointment $office
     */
    public function setOffice($office): void
    {
        $this->office = $office;
    }

    /**
     * @return mixed $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getDoctor()
    {
        return $this->getOffice()->getDoctor();
    }

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    public function getDistanceToUser()
    {
        return $this->distanceToUser;
    }

    public function setDistanceToUser($distanceToUser)
    {
        $this->distanceToUser = $distanceToUser;
    }

    /**
     * @return mixed
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * @param mixed $closed
     */
    public function setClosed($closed): void
    {
        $this->closed = $closed;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary): void
    {
        $this->summary = $summary;
    }

}

