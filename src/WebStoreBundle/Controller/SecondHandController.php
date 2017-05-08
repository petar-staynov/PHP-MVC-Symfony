<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Cart;
use WebStoreBundle\Entity\ItemUsed;

class SecondHandController extends Controller
{
    /**
     * @Route("/market", name="second_hand_market")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function viewSecondHandMarketAction()
    {
        $items = $this->getDoctrine()->getRepository(ItemUsed::class)->findAll();

        if ($items == null) {
            $items = [];
        }
        return $this->render('default/second_hand_market.html.twig', array(
            'items' => $items,
        ));
    }


    /**
     * @Route("/market/item/{id}", name="market_item_view")
     * @param $id
     * @return Response
     */
    public function viewSecondHandItem($id)
    {
        $item = $this
            ->getDoctrine()
            ->getRepository(ItemUsed::class)
            ->find($id);

        if ($item === null) {
            $this->addFlash('danger', 'This item doesn\'t exist');
            $this->redirectToRoute('index');
        }

        return $this->render('market/market_item_view.html.twig', array(
            'item' => $item,
        ));
    }

    /**
     * @Route("/market/myItems", name="market_my_items")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function viewMySecondHandItems()
    {
        $items = $this->getDoctrine()->getRepository(ItemUsed::class)->findBy(['owner' => $this->getUser()]);
        if ($items == null) {
            $items = [];
        }

        return $this->render('default/my_used_items.html.twig', array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/market/myItems/remove/{id}", name="market_my_item_remove")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function removeItemFromMarket($id)
    {
        $em = $this->getDoctrine()->getManager();

        $item = $this->getDoctrine()->getRepository(ItemUsed::class)->find($id);
        if ($item == null) {
            $this->addFlash('danger', 'There is no such item');
            return $this->redirectToRoute('market_my_items');
        }
        if ($item->getOwner() != $this->getUser()) {
            $this->addFlash('danger', 'You do not own this item');
            return $this->redirectToRoute('market_my_items');
        }

        //Removes item from database
        $em->remove($item);

        //Ads item back to purchases
        $userPurchases = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['owner' => $this->getUser()]);
        $itemPrice = $item->getPrice();
        $itemName = $item->getName();
        $itemId = $item->getReferenceId();

        $item= [
            'id' => $itemId,
            'name' => $itemName,
            'price' => $itemPrice,
        ];

        $userPurchases->addItem($item);

        //Updates database
        $em->flush();

        $this->addFlash('success', 'Item removed from market, you can find it in your purchases.');
        return $this->redirectToRoute('market_my_items');
    }
}
