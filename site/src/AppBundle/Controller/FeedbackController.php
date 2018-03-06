<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Form\FeedbackType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FeedbackController extends Controller
{
    /**
     * @Route("/feedback/list", name="feedback_list")
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
     * @Route("/feedback/add", name="feedback_add")
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
    
}
