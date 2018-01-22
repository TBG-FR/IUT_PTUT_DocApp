<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Speciality;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AppointmentsController extends Controller
{
    /**
     * @Route("/search", name="appointments.results")
     */
    public function executeSearchAction(Request $request)
    {
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();

        $data = $request->get('search');

        //TODO: verify data and find all matching appointments

        return $this->render(':appointments:results.html.twig', [
            'specialities' => $specialities,
            'extended' => true
        ]);
    }
}
