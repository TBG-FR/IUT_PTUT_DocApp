<?php

namespace UserBundle\Controller;

use UserBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{
    /**
     * @Route("/user/profile", name="users.index")
     */
    public function indexAction()
    {

        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find($this->getUser()->getId());

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('users.save')
        ]);

        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user", name="users.save", methods={"POST"})
     */
    public function saveAction(Request $request)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find($this->getUser()->getId());

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('users.save')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User created successfully');

            return $this->redirectToRoute('users.index');
        }

        return $this->redirectToRoute('users.index');
    }

}
