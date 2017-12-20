<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Doctor;
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
    public function createAction()
    {
        $specialities = $this->getDoctrine()->getRepository(Speciality::class)->findAll();
        $form = $this->createForm(DoctorType::class, null, [
            'action' => $this->generateUrl('app_doctors_save')
        ]);

        return $this->render('doctors/create.html.twig', [
            'form' => $form->createView(),
            'specialities' => $specialities
        ]);
    }

    /**
     * @Route("/doctors", methods={"POST"})
     */
    public function saveAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = $request->get('doctor');

        $doctor = new Doctor();
        $doctor->setFirstName($data['first_name']);
        $doctor->setLastName($data['last_name']);
        $doctor->setEmail($data['email']);
        $doctor->setUsername(ucfirst($data['first_name'])
            . ' ' . strtoupper($data['last_name']));
        $doctor->setPassword($passwordEncoder->encodePassword($doctor, $data['password']));
        $doctor->setEnabled(false); // disabled by default until we confirm the identity
        $doctor->setPhone($data['phone']);
        //$doctor->setSpecialities($data['phone']);
        $doctor->setAddress($data['address']);
        $doctor->setCity($data['city']);
        $doctor->setZip($data['zip']);

        $validator = $this->get('validator');
        $errors = $validator->validate($doctor);

        if($errors->count() === 0) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($doctor);
            $manager->flush();
        }

        return new Response("");
    }

}