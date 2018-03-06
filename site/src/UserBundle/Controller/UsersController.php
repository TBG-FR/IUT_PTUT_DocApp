<?php

namespace UserBundle\Controller;

use UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\EditUserType;
use UserBundle\Form\UserType;

class UsersController extends Controller
{
    /**
     * @Route("/user/profile", name="users_index")
     */
    public function indexAction()
    {

        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find($this->getUser()->getId());

        $form = $this->createForm(EditUserType::class, $user);

        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/edit", name="users_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $file = $user->getAvatar()->getFile();
            $fileName = uniqid() . '.' . $file->getExtension();
            $user->getAvatar()->setUrl('/uploads/' . $fileName);
            $file->move('uploads', $fileName);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'User edited successfully');

            return $this->redirectToRoute('users_index');
        }

        return $this->redirectToRoute('users_index');

    }

    /**
     * @Route("/user", name="users_save", methods={"GET", "POST"})
     */
    public function saveAction(Request $request)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find($this->getUser()->getId());

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('users_save')
        ]);

        if ($form->isSubmitted() && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User created successfully');

            return $this->redirectToRoute('users_index');
        }

        return $this->redirectToRoute('users_index');
    }

}
