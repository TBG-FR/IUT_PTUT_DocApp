<?php

namespace AppBundle\Controller;

use AppBundle\Service\LocationService;
use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Entity\Speciality;
use AppBundle\Form\FeedbackType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\BrowserKit\Response;
use AppBundle\Form\AppointmentMultipleType;
use Symfony\Component\Validator\Constraints\DateTime;
use UserBundle\Entity\User;
use AppBundle\Entity\Feedback;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FeedbackController extends Controller
{
    /**
     * @Route("/feedback/list", name="appointments.results")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function executeSearchAction(Request $request)
    {

        $feedbacks = $this->getDoctrine()->getRepository(Feedback::class)->findAll();

        return $this->render(':feedback:list.html.twig', [
            'feedbacks' => $feedbacks
        ]);

    }

    /**
     * @Route("/feedback/add", name="feedback.add")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {

        $feedback = new Feedback();
        $form = $this->get('form.factory')->create(FeedbackType::class, $feedback, []);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            return $this->render(':feedback:success.html.twig');

        }

        else {

            return $this->render(':feedback:create.html.twig', [
                'form' => $form->createView()
            ]);

        }
    }

//    /**
//     * @Route("/appt/create/multiple", name="appointments.create.multiple")
//     *
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function createMultipleAction(Request $request)
//    {
//        $appointment = new Appointment();
//        $form = $this->get('form.factory')->create(AppointmentMultipleType::class, $appointment, [
//            'trait_choices' => $this->getUser()
//        ]);
//
//        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
//
//            $date= $request->get('appointment_multiple')['date'];
//            $NbCreneaux = $request->get('appointment_multiple')['NbCrenaux'];
//            $hours = $request->get('appointment_multiple')['DureeCrenaux']['hours']-1;
//            $minutes = $request->get('appointment_multiple')['DureeCrenaux']['minutes'];
//            $description= $request->get('appointment_multiple')['description'];
//
//
//            $office= $request->get('appointment_multiple')['office']; //arrive pas à recuperer l'entity
//            dump($office);
//            $interval = new \DateInterval('PT' . $hours . 'H' . $minutes . 'M');
//            $currentStart=new \DateTime($appointment->getStartTime()->format('H:i:s'));
//            $specialities= $request->get('appointment_multiple')['specialities'];
//            dump($specialities);
//
//            while ($NbCreneaux>=0){
//
//                $appointment = new Appointment();
//                $appointment->setClosed(false);
//                $appointment->setSummary("");
//                $appointment->setStartTime($currentStart);
//                $appointment->setEndTime($currentStart->add($interval));
//                $appointment->setDate($date);
//                $appointment->setDescription($description);
//                $appointment->setOffice($office);
//                $appointment->setSpecialities($specialities);
//
//
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($appointment);
//                $em->flush();
//
//
//
//                $NbCreneaux=$NbCreneaux-1;
//            }
//
//            return $this->redirect($this->generateUrl('doctor_panel'));
//        }
//
//        else {
//
//            return $this->render(':appointments:create_multiple.html.twig', [
//                'form' => $form->createView()
//            ]);
//
//        }
//	}
//
//	/**
//     * @Route("/user/appointments", name="user_appointments")
//     */
//    public function userAppointmentsAction()
//    {
//        $appointments = $this->getUser()->getAppointments();
//
//        return $this->render(':appointments:user_appointments.html.twig', [
//            'appointments' => $appointments
//        ]);
//    }
}
