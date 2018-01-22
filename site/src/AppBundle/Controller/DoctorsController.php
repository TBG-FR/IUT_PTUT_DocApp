<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Doctor;
use AppBundle\Entity\Office;
use AppBundle\Entity\Speciality;
use AppBundle\Entity\User;
use AppBundle\Form\DoctorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();

        $doctor = new Doctor;
        $form = $this->get('form.factory')->create(DoctorType::class, $doctor);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $doctor->setPassword($passwordEncoder->encodePassword($doctor, $doctor->getPassword()));
            $doctor->setEnabled(false);
            $doctor->setUsername(strtolower($doctor->getFirstName().$doctor->getLastName()));
            //create default office
            $office = new Office();
            $office->setAddress($doctor->getAddress());
            $office->setDoctor($doctor);
            $doctor->addOffice($office);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($doctor);
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Vous êtes maintenant inscrit');
            return $this->redirect($this->generateUrl('app_doctors_index'));
        }

        return $this->render('doctors/create.html.twig', [
            'form' => $form->createView(),
            'specialities' => $specialities
        ]);
    }

 }