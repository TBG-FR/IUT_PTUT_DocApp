<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class RegularAppointment extends Appointment
{
    //ENUM of frequencies
    const FREQ_DAILY     = 'DAILY';
    const FREQ_WEEKLY     = 'WEEKLY';
    const FREQ_MONTHLY     = 'MONTHLY';

    static private $frequencyArray = [];

    /**
     * @ORM\Column(name="frequency", type="integer")
     * @Assert\NotBlank()
     *
     * Enter 1 for "every day", 2 for "every two days", etc.
     */
    private $frequency;

    /**
     * @ORM\Column(name="frequency_type", type="string")
     * @Assert\NotBlank()
     *
     * See "FrequencyType" Enum above
     */
    private $frequency_type;

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    public function __construct()
    {
        parent::__construct();
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

    public function setFrequencyType($frequencyType)
    {
        if (!in_array($frequencyType, self::getFrequencyTypeList()))
        {
            throw new \InvalidArgumentException(
                sprintf('Invalid value for RegularAppointment.frequency_type : %s.', $frequencyType)
            );
        }
        $this->frequency_type = $frequencyType;
    }

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    /**
     * @return string
     */
    public function getFrequencyType(): string
    {
        return $this->frequency_type;
    }

    /**
     * @param int $frequency
     */
    public function setFrequency(int $frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * @return int
     */
    public function getFrequency(): int
    {
        return $this->frequency;
    }

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    public function isRegularAppointment()
    {
        return true;
    }

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */
    static public function getFrequencyTypeList()
    {
        // Build frequencyArray if this is the first call
        if (self::$frequencyArray == null)
        {
            self::$frequencyArray = array();
            $oClass         = new \ReflectionClass ('\AppBundle\Entity\Appointment');
            $classConstants = $oClass->getConstants ();
            $constantPrefix = "FREQ_";
            foreach ($classConstants as $key=>$val)
            {
                if (substr($key, 0, strlen($constantPrefix)) === $constantPrefix)
                {
                    self::$frequencyArray[$val] = $val;
                }
            }
        }
        return self::$frequencyArray;
    }

}

