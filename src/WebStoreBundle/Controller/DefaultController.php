<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WebStoreBundle\Entity\Item;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        //All items
        $items = $this->getDoctrine()->getRepository(Item::class)->findAll();

        //My items display if logged in
        $myItems = [];
        if ($this->getUser())
        {
            $myId = $this->getUser()->getId();
            $myItems = $this->getDoctrine()->getRepository(Item::class)->findBy(
                array('ownerId' => $myId)
            );
        }


        return $this->render('default/index.html.twig', array(
            'items' => $items,
            'myItems' => $myItems
        ));
    }
}
