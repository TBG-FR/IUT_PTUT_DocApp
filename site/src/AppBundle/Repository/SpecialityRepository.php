<?php

namespace AppBundle\Repository;

use UserBundle\Entity\Doctor;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;


/**
 * SpecialityRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class SpecialityRepository extends EntityRepository
{
    public function getFindByUserQueryBuilder(Doctor $doctor) : QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.doctors', 'd')
            ->where('d.id = :doc')
            ->setParameter(':doc', $doctor->getId());
    }
}
