<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AppointmentsController extends Controller
{
    /**
     * @Route("/search", name="appointments.results")
     */
    public function executeSearchAction(/* TODO : Inputs */)
    {

        /* TODO : Get the given request and return results */

        return $this->render(':appointments:search_results.html.twig', array(/* TODO : Outputs */));
    }
}
