<?php // src/AppBundle/Controller/LuckyController.php

/* ----- ----- V1 - With render() (Templates) ----- ----- */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LuckyController extends Controller
{
    /**
     * @Route("/lucky/number")
     */
    public function numberAction()
    {
        $number = mt_rand(0, 100);

        return $this->render('lucky/number.html.twig', array(
            'number' => $number,
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
    }
}