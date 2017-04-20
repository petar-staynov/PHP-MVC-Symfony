<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        $usersRepo = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        if($currentUser){
            $money = $currentUser->getMoney();
            return $this->render('default/index.html.twig', array('money' => $money));
        }

        return $this->render('default/index.html.twig');
    }
}
