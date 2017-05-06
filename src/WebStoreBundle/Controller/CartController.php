<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Cart;
use WebStoreBundle\Entity\Item;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart_view")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function indexAction()
    {
        $session = $this->get('session');

        $cart = $session->get('cart');
        if ($cart === null) {
            $cart = [];
            $session->set('cart', $cart);
        }

        $totalCost = 0;
        foreach ($cart as $item) {
            $totalCost += $item['price'] * $item['amount'];
        }


        return $this->render('default/cart.html.twig', array(
            'cart' => $cart,
            'totalCost' => $totalCost,
        ));

    }

    /**
     * @Route("/cart/add/{id}", name="cart_item_add")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function addItemAction($id, Request $request)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        $session = $this->get('session');
        $cart = $session->get('cart');

        //Creates a cart in the session if such doesn't exist
        if (!$cart) {
            $session->set('cart', array());
        }

        //Checks if the item is already in the cart.
        if (!array_key_exists($item->getId(), $cart)) {
            $cart[$item->getId()] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'amount' => 1
            ];
        } else {
            $cart[$item->getId()]['amount']++;
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Item added to cart');
        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/cart/increase/{id}", name="cart_item_increase")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function increaseQuantityAction($id)
    {
        $session = $this->get('session');
        $cart = $session->get('cart');

        $cart[$id]['amount']++;

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_view');
    }

    /**
     * @Route("/cart/decrease/{id}", name="cart_item_decrease")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function decreaseQuantityAction($id)
    {
        $session = $this->get('session');
        $cart = $session->get('cart');

        $cart[$id]['amount']--;
        if ($cart[$id]['amount'] <= 0) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_view');
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_item_remove")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function removeCartItemAction($id)
    {
        $session = $this->get('session');
        $cart = $session->get('cart');

        unset($cart[$id]);

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_view');
    }

    /**
     * @Route("/cart/empty", name="cart_empty")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function emptyCartAction()
    {
        $session = $this->get('session');
        $cart = [];
        $session->set('cart', $cart);

        $this->addFlash('success', 'Cart emptied successfully.');
        return $this->redirectToRoute('cart_view');
    }


    /**
     * @Route("/cart/checkout", name="cart_checkout")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function checkOutAction()
    {
        $session = $this->get('session');
        $cart = $session->get('cart');

        //Checks if items in cart are the same as on server and if there is enough quantity
        $differenceProblem = false;
        $quantityProblem = false;
        $moneyProblem = false;

        $totalCost = 0;
        foreach ($cart as $cartItem) {
            $id = $cartItem['id'];
            $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

            //Checks price
            if ($item->getPrice() != $cartItem['price']) {
                $differenceProblem = true;

                $cartItem['price'] = $item->getPrice();
                $cartItem['name'] = $item->getName();

                $cart[$id] = $cartItem;
                $this->addFlash('warning', 'Some of your cart items have changed since your last review. Please check your cart again.');
            }

            //Checks quantity
            if ($item->getQuantity() < $cartItem['amount']) {
                $quantityProblem = true;

                $this->addFlash('warning', 'There are only ' .
                    $item->getQuantity() .
                    ' ' .
                    $item->getName() .
                    ' left in stock. Please buy less.');
            }
            $totalCost += $item->getPrice();
        }

        //Checks money
        if ($this->getUser()->getMoney() < $totalCost) {
            $moneyProblem = true;

            $this->addFlash('warning', 'You don\'t have enough money');
        }

        $session->set('cart', $cart);
        if ($differenceProblem == true || $quantityProblem == true || $moneyProblem == true) {
            return $this->redirectToRoute('cart_view');
        }

        //TODO Set session, Add cart to database, Update item database entry (quantity)

//        $session->set('cart', []);

        $this->addFlash('success', 'Checkout successful.');
        return $this->redirectToRoute('cart_view');
    }
}