<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditorController extends Controller
{
    /**
     * @Route("/editor", name="editor")
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/editor.html.twig', array());
    }
}
