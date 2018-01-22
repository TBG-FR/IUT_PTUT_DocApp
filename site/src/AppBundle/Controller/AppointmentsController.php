<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Appointment;
use AppBundle\Entity\Speciality;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;

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

    /**
     * @Route("/appt/{id}/details", name="appointments.details")
     */
    public function displayDetailsAction($id, Request $request)
    {
        $appointment = $this->getDoctrine()->getRepository(Speciality::class)->find($id);

        return $this->render(':appointments:details.html.twig', [
            'appointment' => $appointment
            /*'extended' => true*/
        ]);
    }

    /**
     * @Route("/appt/{id}/reservation", name="appointments.reservation")
     */
    public function reserveApptAction($id, Request $request)
    {
        $appointment = $this->getDoctrine()->getRepository(Speciality::class)->find($id);

        return $this->render(':appointments:reservation.html.twig', [
            'appointment' => $appointment
            /*'extended' => true*/
        ]);
    }

    /**
     * @Route("/appt/{id}/success", name="appointments.success")
     */
    public function successAction($id, Request $request)
    {
        $appointment = $this->getDoctrine()->getRepository(Speciality::class)->find($id);

        return $this->render(':appointments:success.html.twig', [
            'appointment' => $appointment
            /*'extended' => true*/
        ]);
    }
}
