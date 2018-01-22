<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Appointment
 *
 * @ORM\Table(name="appointment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppointmentRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn("type", type="string")
 * @ORM\DiscriminatorMap({"REGULAR_APPOINTMENT" = "RegularAppointment", "APPOINTMENT" = "Appointment"})
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

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    public function __construct()
    {
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

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    public function getEndTime()
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

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    public function isRegularAppointment()
    {
        return false;
    }

}

