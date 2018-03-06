<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Speciality;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default_index")
     */
    public function indexAction()
    {
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();

        return $this->render('default/index.html.twig', [
            'specialities' => $specialities,
            'extended' => false
        ]);
    }

    /**
     * @Route("/", name="default_reviews")
     */
    public function reviewsAction()
    {
        return $this->render('default/reviews.html.twig', []);
    }
}
