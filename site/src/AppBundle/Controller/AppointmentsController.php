<?php

namespace AppBundle\Controller;

use AppBundle\Service\LocationService;
use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Entity\Speciality;
use AppBundle\Form\AppointmentType;
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
        dump($minTime,$maxTime);
        $appointmentRepo = $this->getDoctrine()->getRepository(Appointment::class);
        $appointments = $appointmentRepo->getAvailableAppointmentsQueryBuilder()
            ->innerJoin('a.office', 'o')
            ->innerJoin('o.doctor', 'd')
            ->innerJoin('d.specialities', 's')
            ->where('1 = 1 OR (a.startTime >= :time_min AND a.startTime < :time_max AND a.user IS NULL AND s.id = :speciality_id)')
            ->setParameter(':time_min', $minTime)
            ->setParameter(':time_max', $maxTime)
            ->setParameter(':speciality_id', $data['speciality'])
            ->getQuery()
            ->getResult();

        dump($appointments);
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
            dump($clientCoords);
            if(count($clientCoords) === 2) {
                $distance = $locationService->distance($address->getLatitude(), $address->getLongitude(),
                    $clientCoords[0], $clientCoords[1]);
                dump($distance);

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
        ]);
    }

    /**
     * @Route("/appt/success", name="appointments.success")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successAction(Request $request)
    {

        if($request->isMethod('POST')) {

            $id = $request->request->get('paymentSuccessful');
            $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

            /* TODO : IF Appointment is FREE */

            /* TODO : Make link between User & Appt */

            return $this->render(':appointments:success.html.twig', [
                'appointment' => $appointment
            ]);

        }

        else {

            /* TODO : Error Messages */

            return $this->render(':appointments:failure.html.twig', [
                'error' => "errorTODO"
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
            $data = $request->get('appointment');
            if(isset($data['regular_appointment']) && $data['regular_appointment'] == 1) {
                $appointment = new RegularAppointment($appointment);
                //TODO: verify frequency && frequency type
                $appointment->setFrequency($data['frequency']);
                $appointment->setFrequencyType($data['frequency_type']);
            } else {
                $appointment->setDate(new \DateTime());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();

        }

        return $this->render(':appointments:create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
