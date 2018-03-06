<?php

namespace AppBundle\Controller;

use AppBundle\Service\LocationService;
use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Entity\Speciality;
use AppBundle\Entity\Office;
use AppBundle\Form\AppointmentType;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @Route("/search", name="appointments_results")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function executeSearchAction(Request $request, LocationService $locationService)
    {
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();
        $maxDistance = 50; //kilometers
        $minTime = new \DateTime(urldecode($request->get('time')));
        $maxTime = new \DateTime(urldecode($request->get('time')));
        $maxTime->add(new \DateInterval('PT12H'));
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

        if(count($appointments) > 0) {
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

            //sort appointments
            usort($appointments, function($first, $second) {
                $time1 = $this->getDistanceTime($first->getDistanceToUser());
                $time2 = $this->getDistanceTime($second->getDistanceToUser());
                $first->setDistanceToUserTime($time1);
                $second->setDistanceToUserTime($time2);
                $time1 = (clone $first->getStartTime())->add(new \DateInterval('PT' .
                    $time1['hours'] . 'H' .
                    $time1['minutes'] . 'M' .
                    $time1['seconds'] . 'S'
                ));
                $time2 = (clone $second->getStartTime())->add(new \DateInterval('PT' .
                    $time2['hours'] . 'H' .
                    $time2['minutes'] . 'M' .
                    $time2['seconds'] . 'S'
                ));

                if($time1 == $time2) return 0;
                else return ($time1 < $time2) ? -1 : 1;
            });

        }

        return $this->render(':appointments:display_results.html.twig', [
            'specialities' => $specialities,
            'startTime' => $minTime,
            'appointments' => $appointments,
            'extended' => false,
            'myLoc' => $clientCoords
        ]);
    }

    private function getDistanceTime(float $distance)
    {
        $speed = 70;//kmh
        $factor = (1/$speed) * $distance;
        $hours = floor($factor);
        $minutes = ($factor - floor($factor)) * 60;
        $seconds = floor(($minutes - floor($minutes)) * 60);
        $minutes = floor($minutes);

        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds
        ];
    }

    /**
     * @Route("/appt/{id}/details", name="appointments_details")
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

            return $this->render(':appointments:display_details.html.twig', [
                'appointment' => $appointment,
                'specialities' => $specialities,
                'extended' => false
            ]);

        }
    }

    /**
     * @Route("/appt/{id}/reservation", name="appointments_reservation")
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

            return $this->render(':reservation:laststep.html.twig', [
                'appointment' => $appointment
            ]);

        }
    }

    /**
     * @Route("/appt/reservation/result", name="appointments_reservation_result")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reservationResultAction(Request $request)
    {

        if($request->isMethod('POST')) { // IF the user acceeded to that page with POST (= after a payment)

            $id = $request->request->get('paymentSuccessful');
            $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

            if($appointment->getUser() instanceof User || $appointment->getUser() instanceof Doctor) {

                //TODO : FLASHBAG

                /*
                <div class="alert alert-danger" role="alert">
                    Votre rendez-vous n'a pas été réservé (paiement non effectué) !
                    Your appointment hasn't been reserved (payment not made) !
                </div>
                */

                return $this->render(':reservation:failure.html.twig', [
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

                return $this->render(':reservation:success.html.twig', [
                    'appointment' => $appointment
                ]);

            }

        }

        else { // ELSE (the user acceeded to that page manually)

            //throw $this->createNotFoundException("You didn't made any reservation or payment !");
            throw $this->createNotFoundException("Vous n'avez fait aucune réservation ni paiement !");

        }

    }

    /**
     * @Route("/panel/appt/create/single", name="appointments_create_single")
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

            if($appointment->getSpecialities()->isEmpty()) { // IF the user entered no speciality

                $this->get('session')->getFlashBag()->add('danger','<span class="fa fa-warning"></span> Vous devez sélectionner <strong>au moins une spécialité</strong> !');
                $this->get('session')->getFlashBag()->add('danger','<span class="fa fa-warning"></span> You must choose <strong>at least one speciality</strong> !');

                return $this->render(':appointments:create_single.html.twig', [
                    'form' => $form->createView()
                ]);

            }

            else { // ELSE (the user entered at least one speciality)

            //$appointment->setDate(new \DateTime());
            //$interval=$request->request->get('duration');

            $hours = $request->get('appointment')['duration']['hours']-1;
            $minutes = $request->get('appointment')['duration']['minutes'];
            $interval = new \DateInterval('PT' . $hours . 'H' . $minutes . 'M');
            $start=new \DateTime($appointment->getStartTime()->format('H:i:s'));

            $appointment->setEndTime($start->add($interval));
            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();

            return $this->redirect($this->generateUrl('doctor_panel'));

            }
        }

        else {

            return $this->render(':appointments:create_single.html.twig', [
                'form' => $form->createView()
            ]);

        }
    }

    /**
     * @Route("/panel/appt/create/multiple", name="appointments_create_multiple")
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

            $date= new \DateTime($request->get('appointment_multiple')['date']['year']."-".$request->get('appointment_multiple')['date']['month'].'-'.$request->get('appointment_multiple')['date']['day']);
            $NbCreneaux = $request->get('appointment_multiple')['NbCrenaux'];
            $hours = $request->get('appointment_multiple')['DureeCrenaux']['hours']-1;
            $minutes = $request->get('appointment_multiple')['DureeCrenaux']['minutes'];
            $description= $request->get('appointment_multiple')['description'];

            $office = $this->getDoctrine()
                ->getRepository(Office::class)
                ->find($request->get('appointment_multiple')['office']);

            //dump($office);
            $interval = new \DateInterval('PT' . $hours . 'H' . $minutes . 'M');
            $currentStart=new \DateTime($appointment->getStartTime()->format('H:i:s'));

            $spe= $request->get('appointment_multiple')['specialities'];
            $specialities=new ArrayCollection();
            foreach ($spe as $s){
                $specialities->add(  $this->getDoctrine()
                    ->getRepository(Speciality::class)
                    ->find($s));
            }

            $doc=$office->getDoctor();

            dump($doc);
            $appointmentsDoc= $this->getDoctrine()
                ->getRepository(Appointment::class)
                ->findBy(
                    ['office' => $office->getID()],
                        ['id' => 'ASC']
                    );

            foreach ($appointmentsDoc as $appointmentDoc) {
                $appointmentDoc->setStartTime(new \DateTime($appointmentDoc->getStartTime()->format('H:i:s')));
                $appointmentDoc->setEndTime(new \DateTime($appointmentDoc->getEndTime()->format('H:i:s')));
            }
            //dump($appointmentsDoc);
            //die();
            while ($NbCreneaux>0){

                $appointment = new Appointment();
                $appointment->setClosed(false);
                $appointment->setSummary("");
                $appointment->setStartTime(new \DateTime($currentStart->format('H:i:s')));
                $appointment->setEndTime($currentStart->add($interval));
                $appointment->setDate($date);
                $appointment->setDescription($description);
                $appointment->setOffice($office);
                $appointment->setSpecialities($specialities);

                foreach ($appointmentsDoc as $appointmentDoc){

                    if($appointmentDoc->getDate()==$appointment->getDate()){
                        if(($appointmentDoc->getStartTime()>$appointment->getStartTime()&&$appointmentDoc->getStartTime()<$appointment->getEndTime())||
                            ($appointmentDoc->getEndTime()>=$appointment->getStartTime()&&$appointmentDoc->getEndTime()<=$appointment->getEndTime())||
                            ($appointmentDoc->getStartTime()<$appointment->getStartTime()&&$appointmentDoc->getEndTime()>$appointment->getEndTime())||($appointmentDoc->getStartTime()>$appointment->getStartTime()&&$appointmentDoc->getEndTime()<$appointment->getEndTime())){

                            return $this->render(':appointments:create_multiple.html.twig', [
                                'form' => $form->createView()
                            ]);
                        }
                    }
                }

                dump($appointment);

                $em = $this->getDoctrine()->getManager();
                $em->persist($appointment);
                $em->flush();
                $NbCreneaux=$NbCreneaux-1;
            }
            return $this->redirect($this->generateUrl('doctor_panel'));
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
        $appointments = $this->getUser()->getAppointments();

        return $this->render(':appointments:user_appointments.html.twig', [
            'appointments' => $appointments
        ]);
    }
}
