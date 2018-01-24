<?php

namespace AppBundle\Controller;

use AppBundle\Service\LocationService;
use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Entity\Speciality;
use AppBundle\Form\AppointmentType;
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
        $data = $request->get('search');
        $minTime = new \DateTime($data['time']);
        $maxTime = new \DateTime($data['time']);
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
            ->innerJoin('o.doctor', 'd')
            ->innerJoin('d.specialities', 's')
            ->where('a.startTime >= :time_min AND a.startTime < :time_max AND a.date = :date AND s.id = :speciality_id')
            ->setParameter(':time_min', $minTime)
            ->setParameter(':time_max', $maxTime)
            ->setParameter(':speciality_id', $data['speciality'])
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
            $clientCoords = explode(',', $data['coords']);

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
            'startTime' => $data['time'],
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

        return $this->render(':appointments:details.html.twig', [
            'appointment' => $appointment
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

        return $this->render(':holding:laststep.html.twig', [
            'appointment' => $appointment
        ]);
    }

    /**
     * @Route("/appt/reservation_result", name="appointments.reservation_result")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successAction(Request $request)
    {

        if($request->isMethod('POST')) {

            $id = $request->request->get('paymentSuccessful');
            $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

            dump($appointment->getUser());

            if($appointment->getUser() instanceof User || $appointment->getUser() instanceof Doctor) {

                /* TODO : Error Messages */

                return $this->render(':holding:failure.html.twig', [
                    'error' => "errorTODO_AlreadyTaken",
                    'appointment' => $appointment
                ]);

            }

            else {

                $appointment->setUser($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($appointment);
                $em->flush();

                return $this->render(':holding:success.html.twig', [
                    'appointment' => $appointment
                ]);

            }

        }

        else {

            /* TODO : Error Messages */

            return $this->render(':holding:failure.html.twig', [
                'error' => "errorTODO_NoPOST"
            ]);

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
            //$appointment->setDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();

            return $this->render('reservation_success.html.twig', [ /* TODO : REDIRECT ON MANAGE PAGE */ ]);
        }

        else {

            return $this->render(':appointments:create.html.twig', [
                'form' => $form->createView()
            ]);

        }
    }
}
