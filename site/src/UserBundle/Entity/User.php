<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"email","username"})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn("discr", type="string")
 * @ORM\DiscriminatorMap({"user"= "User", "Doctor" = "Doctor"})
 */
class User extends BaseUser
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
     * @var string
     *
     * @ORM\Column(name="first_name", type="string")
     */
    private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $last_name;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist","remove"})
     * @ORM\JoinColumn("avatar_id", referencedColumnName="id", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Appointment", mappedBy="user")
     */
    private $appointments;

    public function __construct()
    {
        parent::__construct();
        $this->appointments = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return bool
     */
    public function isDoctor()
    {
        return false;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar)
    {
        $this->avatar= $avatar;
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

