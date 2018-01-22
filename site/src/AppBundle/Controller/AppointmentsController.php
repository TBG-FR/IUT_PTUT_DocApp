<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Entity\Speciality;
use AppBundle\Form\AppointmentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;

class AppointmentsController extends Controller
{
    /**
     * @Route("/search", name="appointments.results")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
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
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayDetailsAction($id, Request $request)
    {
        $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

        return $this->render(':appointments:details.html.twig', [
            'appointment' => $appointment
            /*'extended' => true*/
        ]);
    }

    /**
     * @Route("/appt/{id}/reservation", name="appointments.reservation")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reserveApptAction($id, Request $request)
    {
        $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

        return $this->render(':appointments:reservation.html.twig', [
            'appointment' => $appointment
            /*'extended' => true*/
        ]);
    }

    /**
     * @Route("/appt/{id}/success", name="appointments.success")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successAction($id, Request $request)
    {
        $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

        return $this->render(':appointments:success.html.twig', [
            'appointment' => $appointment
            /*'extended' => true*/
        ]);
    }

    /**
     * @Route("/appt/create", name="appointments.create")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $appointment = new Appointment();
        $form = $this->get('form.factory')->create(AppointmentType::class, $appointment, [
            'trait_choices' => $this->getUser()
        ]);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->get('appointment');
            if(isset($data['regular_appointment']) && $data['regular_appointment'] == 1) {
                $appointment = new RegularAppointment($appointment);
                //TODO: verify frequency && frequency type
                $appointment->setFrequency($data['frequency']);
                $appointment->setFrequencyType($data['frequency_type']);

                $em = $this->getDoctrine()->getManager();
                $em->persist($appointment);
                $em->flush();
            }

        }

        return $this->render(':appointments:create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
