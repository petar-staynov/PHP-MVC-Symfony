<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use WebStoreBundle\Entity\Item;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        //All items
        $items = $this->getDoctrine()->getRepository(Item::class)->findAll();

        //My items and money display if logged in
        if ($this->getUser()) {
            $currentUser = $this->getUser();
            $userMoney = $currentUser->getMoney();
            $session->set('myMoney', $userMoney);
        }

        return $this->render('default/index.html.twig', array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/about", name="about_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutPageAction()
    {
        return $this->render('default/about.html.twig');
    }
}
