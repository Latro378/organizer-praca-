<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;


class CallendarController extends Controller
{
    /**
     * @Route("/calendar", name="calendar_index")
     */
    public function calendarAction()
    {
        return $this->render('calendar/calendar.html.twig');
    }


}
