<?php

namespace AppBundle\Controller;

use AppBundle\Service\LocationService;
use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Entity\Speciality;
use AppBundle\Form\AppointmentType;
use Symfony\Component\BrowserKit\Response;
use AppBundle\Form\AppointmentMultipleType;
use Symfony\Component\Validator\Constraints\DateTime;
use UserBundle\Entity\User;
use UserBundle\Entity\Doctor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AppointmentsController extends Controller
{
    /**
     * @Route("/search", name="appointments.results")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function executeSearchAction(Request $request, LocationService $locationService)
    {
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();
        $maxDistance = 50; //kilometers
        $minTime = new \DateTime($request->get('time'));
        $maxTime = new \DateTime($request->get('time'));
        $maxTime->add(new \DateInterval('PT8H'));
        // si on change de jour, on arrête la recherche à minuit le jour même
        if($maxTime->format('d') != $minTime->format('d')) {
            $maxTime = new \DateTime(date('Y-m-d'));
            $maxTime->add(new \DateInterval('PT23H59M59S'));
        }
        $date = new \DateTime();
        $appointmentRepo = $this->getDoctrine()->getRepository(Appointment::class);
        $appointments = $appointmentRepo->getAvailableAppointmentsQueryBuilder()
            ->innerJoin('a.office', 'o')
            ->innerJoin('a.specialities', 's')
            ->where('a.startTime >= :time_min AND a.startTime < :time_max AND a.date = :date AND s.id = :speciality_id')
            ->setParameter(':time_min', $minTime)
            ->setParameter(':time_max', $maxTime)
            ->setParameter(':speciality_id', $request->get('speciality'))
            ->setParameter(':date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
        $clientCoords = [];
        foreach($appointments as $k => $appointment) {
            /** @var Appointment $appointment */
            $address = $appointment->getOffice()->getAddress();
            if($address->getLatitude() == null
                || $address->getLongitude() == null) {
                $coordinates = $locationService->getCoordinatesFromString($address->toAddressString());
                $address->setLatitude($coordinates['latitude']);
                $address->setLongitude($coordinates['longitude']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($address);
                $em->flush();
            }
            $clientCoords = explode(',', $request->get('coords'));

            if(count($clientCoords) === 2) {
                $distance = $locationService->distance($address->getLatitude(), $address->getLongitude(),
                    $clientCoords[0], $clientCoords[1]);

                if($distance > $maxDistance) {
                    unset($appointments[$k]);
                    continue;
                }
                $appointment->setDistanceToUser($distance);
            }
        }

        //TODO: verify data and find all matching appointments

        return $this->render(':appointments:results.html.twig', [
            'specialities' => $specialities,
            'startTime' => $minTime,
            'appointments' => $appointments,
            'extended' => false,
            'myLoc' => $clientCoords
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
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();

        if(is_null($appointment)) { // IF this appointment doesn't exist

            //throw $this->createNotFoundException("This appointment doesn't exist !");
            throw $this->createNotFoundException("Ce rendez-vous n'existe pas !");

        }

        else { // ELSE (this appointment exists)

            return $this->render(':appointments:details.html.twig', [
                'appointment' => $appointment,
                'specialities' => $specialities,
                'extended' => false
            ]);

        }
    }

    /**
     * @Route("/appt/{id}/reservation", name="appointments.reservation")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reservationAction($id, Request $request)
    {
        $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

        if(is_null($appointment)) { // IF this appointment doesn't exist

            //throw $this->createNotFoundException("This appointment doesn't exist !");
            throw $this->createNotFoundException("Ce rendez-vous n'existe pas !");

        }

        elseif(!is_null($appointment->getUser())) { // ELSE IF this appointment is taken

            //throw $this->createNotFoundException("This appointment is already taken !");
            throw $this->createNotFoundException("Ce rendez-vous est déjà pris !");

        }

        else { // ELSE (this appointment exists and is free)

            return $this->render(':holding:laststep.html.twig', [
                'appointment' => $appointment
            ]);

        }
    }

    /**
     * @Route("/appt/reservation/result", name="appointments.reservation_result")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reservationResultAction(Request $request)
    {

        if($request->isMethod('POST')) { // IF the user acceeded to that page after a payment

            $id = $request->request->get('paymentSuccessful');
            $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

            if($appointment->getUser() instanceof User || $appointment->getUser() instanceof Doctor) { //

                return $this->render(':holding:failure.html.twig', [
                    'error' => "already_taken",
                    'appointment' => $appointment
                ]);

            }

            else {

                // Add the current User to that Appointment
                $appointment->setUser($this->getUser());

                // Apply modifications into EM & DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($appointment);
                $em->flush();

                return $this->render(':holding:success.html.twig', [
                    'appointment' => $appointment
                ]);

            }

        }

        else { // ELSE (the user acceeded to that page manually)

            //throw $this->createNotFoundException("You didn't made any holding or payment !");
            throw $this->createNotFoundException("Vous n'avez fait aucune réservation ni paiement !");

        }

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

            /**/

            //$appointment->setDate(new \DateTime());
            //$interval=$request->request->get('duration');

            $hours = $request->get('appointment')['duration']['hours']-1;
            $minutes = $request->get('appointment')['duration']['minutes'];
            $interval = new \DateInterval('PT' . $hours . 'H' . $minutes . 'M');
            $start=new \DateTime($appointment->getStartTime()->format('H:i:s'));

            $appointment->setEndTime($start->add($interval));
            dump($appointment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();

            return $this->redirect($this->generateUrl('doctor_panel'));
        }

        else {

            return $this->render(':appointments:create.html.twig', [
                'form' => $form->createView()
            ]);

        }
    }

    /**
     * @Route("/appt/create/multiple", name="appointments.create.multiple")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createMultipleAction(Request $request)
    {
        $appointment = new Appointment();
        $form = $this->get('form.factory')->create(AppointmentMultipleType::class, $appointment, [
            'trait_choices' => $this->getUser()
        ]);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            /**/

            //$appointment->setDate(new \DateTime());
            //$interval=$request->request->get('duration');

            $hours = $request->get('appointment')['duration']['hours']-1;
            $minutes = $request->get('appointment')['duration']['minutes'];
            $interval = new \DateInterval('PT' . $hours . 'H' . $minutes . 'M');
            $start=new \DateTime($appointment->getStartTime()->format('H:i:s'));

            $appointment->setEndTime($start->add($interval));
            dump($appointment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();
            return $this->render('holding_success.html.twig', [ /* TODO : REDIRECT ON MANAGE PAGE */ ]);
        }

        else {

            return $this->render(':appointments:create_multiple.html.twig', [
                'form' => $form->createView()
            ]);

        }
	}
		
	/**
     * @Route("/user/appointments", name="user_appointments")
     */
    public function userAppointmentsAction()
    {
        $appointments = $this->getDoctrine()->getRepository(Appointment::class)
            ->getByUser($this->getUser());

        return $this->render(':appointments:user_appointments.html.twig', [
            'appointments' => $appointments
        ]);
    }
}
