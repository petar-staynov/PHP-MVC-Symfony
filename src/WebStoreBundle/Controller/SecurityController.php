<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="user_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request){
        return $this->render('security/login.html.twig');
    }
}
