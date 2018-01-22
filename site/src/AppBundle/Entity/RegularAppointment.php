<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class RegularAppointment extends Appointment
{

    /* @var int
     *
     * @ORM\Column(name="freq", type="integer")
     * @Assert\NotBlank()
     *
     * Enter 1 for "every day", 2 for "every two days", etc.
     */
    private $frequency;

    /* @var string
     *
     * @ORM\Column(name="freq_type", type="string")
     * @Assert\NotBlank()
     *
     * See "FrequencyType" Enum above
     */
    private $frequencyType;

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    public function __construct()
    {
        parent::__construct();
    }

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    //ENUM of frequencies
    const FREQ_DAY     = 'Day';
    const FREQ_WEEK     = 'Week';
    const FREQ_MONTH     = 'Month';

    static private $frequencyArray = null;

    static public function getFrequencyTypeList()
    {
        // Build frequencyArray if this is the first call
        if (self::$frequencyArray == null)
        {
            self::$frequencyArray = array();
            $oClass         = new \ReflectionClass ('\AppBundle\Entity\PunctualAppointment');
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

    public function setFrequencyType($frequencyType)
    {
        if (!in_array($frequencyType, self::getFrequencyTypeList()))
        {
            throw new \InvalidArgumentException(
                sprintf('Invalid value for RegularAppointment.frequencyType : %s.', $frequencyType)
            );
        }
        $this->frequencyType = $frequencyType;
    }

    /* ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- */

    /**
     * @return string
     */
    public function getFrequencyType(): string
    {
        return $this->frequencyType;
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

    public function isRegular()
    {
        return true;
    }
}

