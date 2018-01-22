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
    const FANCY_FREQ_DAILY = 'Journaliere';
    const FANCY_FREQ_WEEKLY = 'Semestrielle';
    const FANCY_FREQ_MONTHLY = 'Mensuelle';

    static private $frequencyArray = [];
    static private $fancyFrequencyArray = [];

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

    public function __construct(Appointment $original = null)
    {
        parent::__construct();

        if($original != null) {
            $this->setStartTime($original->getStartTime());
            $this->setEndTime($original->getEndTime());
            $this->setDate($original->getDate());
            $this->setDescription($original->getDescription());
            $this->setOffice($original->getOffice());
        }
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
            $oClass         = new \ReflectionClass ('\AppBundle\Entity\RegularAppointment');
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

    static public function getFancyFrenquencyTypeList()
    {
        // Build frequencyArray if this is the first call
        if (self::$fancyFrequencyArray == null)
        {
            self::$fancyFrequencyArray = array();
            $oClass         = new \ReflectionClass ('\AppBundle\Entity\RegularAppointment');
            $classConstants = $oClass->getConstants ();
            $constantPrefix = "FANCY_FREQ_";
            foreach ($classConstants as $key=>$val)
            {
                $constantName = substr($key, strlen($constantPrefix), strlen($key)-strlen($constantPrefix));
                if (substr($key, 0, strlen($constantPrefix)) === $constantPrefix)
                {
                    self::$fancyFrequencyArray[$val] = $constantName;
                }
            }
        }
        return self::$fancyFrequencyArray;
    }

}

