<?php // src/AppBundle/Controller/FirstPageController.php

/* ----- ----- V1 - With render() (Templates) ----- ----- */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class FirstPageController extends Controller
{    
    /**
     * Matches /tests/1 exactly
     *
     * @Route("/tests/1", name="test_page_1")
     */
    public function TestOneAction()
    {
        $username = "Bobby";
        
        $this->addFlash('notice', 'Notice : Your changes were saved!');
        $this->addFlash('warning', 'Warning : Your changes were saved!');
        $this->addFlash('error', 'Error : Your changes were saved!');
        
        // store an attribute for reuse during a later user request    
        //$session->set('username', $username);
        
        return $this->render('tests/testpage.html.twig', array(
            'username' => $username,
        ));
    }

}

/* ----- ----- V1 - With $max (Routing) ----- ----- */

//namespace AppBundle\Controller;
//
//use Symfony\Component\HttpFoundation\Response;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//
//class LuckyController
//{
//    /**
//     * @Route("/lucky/number/{max}")
//     */
//    public function numberAction($max)
//    {
//        $number = mt_rand(0, $max);
//
//        return new Response(
//            '<html><body>Lucky number: '.$number.'</body></html>'
//        );
//    }
//}

/* ----- ----- V3 - With a controller ----- ----- */

//namespace AppBundle\Controller;
//
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Psr\Log\LoggerInterface;
//
//class LuckyController extends Controller
//{
//    /**
//     * @Route("/lucky/number")
//     */
//    public function numberAction(LoggerInterface $logger)
//    {
//        $number = mt_rand(0, 100);
//
//        $logger->info('We are logging!');
//
//        return $this->render('lucky/number.html.twig', array(
//            'number' => $number,
//        ));
//    }
//}