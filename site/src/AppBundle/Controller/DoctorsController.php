<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Doctor;
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
     * @Route("/doctors/register")
     */
    public function createAction()
    {
        $form = $this->createForm(DoctorType::class, [
            'action' => $this->generateUrl('app_doctors_save')
        ]);

        return $this->render('doctors/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/doctors", methods={"POST"})
     */
    public function saveAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = $request->get('user');

        $user = new User();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setUsername(ucfirst($data['first_name'])
            . ' ' . strtoupper($data['last_name']));
        $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
        $user->setEnabled(false);

        $doctor = new Doctor();
        $doctor->setPhone($data['phone']);
        $doctor->setSpecialities($data['phone']);
        $doctor->setAddress($data['address']);
        $doctor->setCity($data['city']);
        $doctor->setZip($data['zip']);
        $doctor->setUser($user);

        $validator = $this->createDoctorValidator();
        $errors = $validator->validate($doctor);


        if($errors->count() === 0) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($doctor);
            $manager->flush();
        }

        return new Response("");
    }

    private function createDoctorValidator() : ValidatorInterface
    {
        $validator = $this->get('validator');

        return $validator;
    }
}