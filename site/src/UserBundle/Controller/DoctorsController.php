<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Appointment;
use AppBundle\Entity\Office;
use AppBundle\Entity\Speciality;
use AppBundle\Service\LocationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use UserBundle\Entity\Doctor;
use UserBundle\Form\DoctorType;

class DoctorsController extends Controller
{
    /**
     * @Route("/panel",name="doctor_panel")
     */
    public function index()
    {
        $apptRepo = $this->getDoctrine()->getRepository(Appointment::class);
        $appointments = $apptRepo->getAppointmentsByDoctorQueryBuilder($this->getUser())->getQuery()->getResult();

        return $this->render('doctors/index.html.twig', [
            'appointments' => $appointments
        ]);
    }

    /**
     * @Route("/doctors/register")
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $locationService = new LocationService();
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();

        $doctor = new Doctor;
        $form = $this->get('form.factory')->create(DoctorType::class, $doctor);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $doctor->setPassword($passwordEncoder->encodePassword($doctor, $doctor->getPassword()));
            $doctor->setEnabled(false);
            $doctor->addRole('ROLE_DOCTOR');
            $doctor->setUsername(strtolower($doctor->getFirstName().$doctor->getLastName()));
            //update address coords
            $coords = $locationService->getCoordinatesFromString($doctor->getAddress()->toAddressString());
            $doctor->getAddress()->setLatitude($coords['latitude']);
            $doctor->getAddress()->setLongitude($coords['longitude']);
            //create default office
            $office = new Office();
            $office->setAddress($doctor->getAddress());
            $office->setDoctor($doctor);
            $office->setName($doctor->getAddress()->getName());
            $doctor->addOffice($office);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($doctor);
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Vous êtes maintenant inscrit');
            return $this->redirect($this->generateUrl('doctor_panel'));
        }

        return $this->render('doctors/create.html.twig', [
            'form' => $form->createView(),
            'specialities' => $specialities
        ]);
    }

    /**
     * @Route("/panel/appt/{id}", name="doctor_appointment_details", requirements={"id":"[0-9]+"})
     * @param Appointment $appointment
     */
    public function apptDetailsAction(Appointment $appointment, Request $request)
    {
        if($request->isMethod('POST')) {
            $content = $request->get('details');
            $appointment->setSummary($content);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Rendez-vous clôturé');
            $this->redirect($this->generateUrl('doctor_panel'));
        }

        return $this->render('doctors/appointment_details.html.twig', [
            'appointment' => $appointment
        ]);
    }

 }