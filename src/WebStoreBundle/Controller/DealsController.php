<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Form\ItemType;

class DealsController extends Controller
{
    /**
     * @Route("/deals", name="deals")
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        //Discounted items
        $deals = $this->getDoctrine()->getRepository(Item::class)->findBy(array('discounted' => 1));

        return $this->render('default/deals.html.twig', array(
            'items' => $deals,
        ));
    }
}
