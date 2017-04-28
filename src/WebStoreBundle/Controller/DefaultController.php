<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Form\ItemType;
use Symfony\Component\HttpFoundation\Session\Session;

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
        $userItems = [];
        $userMoney = null;

        if ($this->getUser()) {
            $currentUser = $this->getUser();
            $userId = $currentUser->getId();
            $userMoney = $currentUser->getMoney();
            $userItems = $this->getDoctrine()->getRepository(Item::class)->findBy(
                array('ownerId' => $userId)
            );
            $session->set('myMoney', $userMoney);
        }


        return $this->render('default/index.html.twig', array(
            'items' => $items,
            'myItems' => $userItems,
        ));
    }
}
