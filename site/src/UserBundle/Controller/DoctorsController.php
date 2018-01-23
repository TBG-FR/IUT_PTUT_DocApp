<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Office;
use AppBundle\Entity\Speciality;
use UserBundle\Form\DoctorType;
use AppBundle\Service\LocationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use UserBundle\Entity\Doctor;

class DoctorsController extends Controller
{
    /**
     * @Route("/doctors")
     */
    public function index()
    {
        return $this->render('doctors/index.html.twig');
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
            return $this->redirect($this->generateUrl('user_doctors_index'));
        }

        return $this->render('doctors/create.html.twig', [
            'form' => $form->createView(),
            'specialities' => $specialities
        ]);
    }

 }