<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Cart;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Entity\ItemUsed;

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


        return $this->render('cart/cart.html.twig', array(
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
        if ($cart === null) {
            $cart = [];
            $session->set('cart', $cart);
        }

        //Creates a cart in the session if such doesn't exist
        if (!$cart) {
            $session->set('cart', array());
        }

        //Checks if the item is already in the cart session.
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
        if ($cart === null) {
            $cart = [];
            $session->set('cart', $cart);
        }
        if (count($cart) == 0) {
            $this->addFlash('warning', 'Your cart is empty.');
            $this->redirectToRoute('cart_view');
        }

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
        if ($cart === null) {
            $cart = [];
            $session->set('cart', $cart);
        }
        if (count($cart) == 0) {
            $this->addFlash('warning', 'Your cart is empty.');
            $this->redirectToRoute('cart_view');
        }

        $cart[$id]['amount']--;
        if ($cart[$id]['amount'] <= 0) {
            unset($cart[$id]);
            $cart = array_values($cart);
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
        if ($cart === null) {
            $cart = [];
            $session->set('cart', $cart);
        }
        if (count($cart) == 0) {
            $this->addFlash('warning', 'Your cart is empty.');
            $this->redirectToRoute('cart_view');
        }

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
        $cartSession = $session->get('cart');
        if ($cartSession === null) {
            $cartSession = [];
            $session->set('cart', $cartSession);
        }
        if (count($cartSession) == 0) {
            $this->addFlash('warning', 'Your cart is empty.');
            return $this->redirectToRoute('cart_view');
        }

        //Checks if items in cart are the same as on server and if there is enough quantity
        $differenceProblem = false;
        $quantityProblem = false;
        $moneyProblem = false;

        $totalCost = 0;
        foreach ($cartSession as $cartItem) {
            $id = $cartItem['id'];
            $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

            //Checks price and updates it
            if ($item->getPrice() != $cartItem['price']) {
                $differenceProblem = true;

                $cartItem['price'] = $item->getPrice();
                $cartItem['name'] = $item->getName();

                $cartSession[$id] = $cartItem;
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

        //Updates items in session
        $session->set('cart', $cartSession);

        //Checks money
        if ($this->getUser()->getMoney() < $totalCost) {
            $moneyProblem = true;

            $this->addFlash('warning', 'You don\'t have enough money');
        }

        //Final check
        if ($differenceProblem == true || $quantityProblem == true || $moneyProblem == true) {
            return $this->redirectToRoute('cart_view');
        }


        /////////////////Gets user's previous cart from DB or makes on if there is none//////
        $em = $this->getDoctrine()->getManager();

        //Makes purchases cart if there isnt one
        $cartEntity = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['owner' => $this->getUser()]);
        if ($cartEntity == null) {
            $cartEntity = new Cart();
            $cartEntity->setOwner($this->getUser());
            $this->getUser()->setCart($cartEntity);
            $em->persist($cartEntity);
        }


        //Updates item quantity in DB
        foreach ($cartSession as $cartItem) {
            $dbItem = $this->getDoctrine()->getRepository(Item::class)->find($cartItem['id']);
            $dbItemNewQuantity = $dbItem->getQuantity() - $cartItem['amount'];
            $dbItem->setQuantity($dbItemNewQuantity);

            //Adds cart session item to cart entity multiplied by its quantity
            for ($i = 0; $i < $cartItem['amount']; $i++){
                $cartEntity->addItem($cartItem);
            }
        }

        //Lowers money
        $userMoney = $this->getUser()->getMoney();
        $newUserMoney = $userMoney-$totalCost;
        $this->getUser()->setMoney($newUserMoney);
        $session->set('myMoney', $newUserMoney);

        //Updates DB and clears session cart
        $em->flush();
        $session->set('cart', []);

        $this->addFlash('success', 'Checkout successful.');
        return $this->redirectToRoute('cart_view');
    }

    /**
     * @Route("/cart/history", name="cart_purchases")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function viewBoughtItemsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $userId = $user->getId();

        //Creates database cart for the user if there isnt one
        $userItemsEntity = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['owner' => $user]);
        if ($userItemsEntity == null) {
            $userItemsEntity = new Cart();
            $userItemsEntity->setOwner($this->getUser());
            $userItemsEntity->setItems([]);
            $this->getUser()->setCart($userItemsEntity);
            $em->persist($userItemsEntity);
            $em->flush();
            $this->redirectToRoute('cart_purchases');
        }

        $userItemsArr = $userItemsEntity->getItems();


        $items = [];
        foreach ($userItemsArr as $userItem){
            $items[] = $userItem;
        }
        return $this->render('cart/cart_history.html.twig', array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/cart/history/sell/{index}", name="cart_purchases_sell")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $index
     * @return Response
     */
    public function sellBoughtItemAction($index)
    {
        $index = $index-1;
        $em = $this->getDoctrine()->getManager();

        $cartEntity = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['owner' => $this->getUser()]);
        if ($cartEntity == null){
            $this->addFlash('danger', 'No such item');
            return $this->redirectToRoute('cart_purchases');
        }

        $itemsArr = $cartEntity->getItems();
        $itemsArrKeys = array_keys($itemsArr);

        $currentItem = $itemsArr[$itemsArrKeys[$index]];

        $originalItem = $this->getDoctrine()->getRepository(Item::class)->findOneBy(['id' => $currentItem['id']]);

        //Creates second hand copy of original item
        $usedItemEntity = new ItemUsed();
        $usedItemEntity->setOwner($this->getUser());
        $usedItemEntity->setOwnerId($this->getUser()->getId());
        $usedItemEntity->setName($currentItem['name']);
        $usedItemEntity->setDescription($originalItem->getDescription());
        $usedItemEntity->setPrice($currentItem['price']);
        $usedItemEntity->setImageName($originalItem->getImageName());
        $usedItemEntity->setReferenceId($originalItem->getId());

        //Removes item being sold from db cart
        unset($itemsArr[$index]);
        $itemsArr = array_values($itemsArr);

        $cartEntity->setItems($itemsArr);

        $em->persist($usedItemEntity);
        $em->flush();


        $this->addFlash('success', 'Item put up for sale');
        return $this->redirectToRoute('cart_purchases');
    }
    /**
     * Removes item from previous purchases.
     *
     * @Route("/cart/history/claim/{index}", name="cart_purchases_claim")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $index
     * @return Response
     */
    public function claimBoughtItemAction($index){
        $index = $index-1;

        $em = $this->getDoctrine()->getManager();
        $cartEntity = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['owner' => $this->getUser()]);
        if ($cartEntity == null){
            $this->addFlash('danger', 'No such item');
            return $this->redirectToRoute('cart_purchases');
        }
        $itemsArr = $cartEntity->getItems();

        unset($itemsArr[$index]);
        $itemsArr = array_values($itemsArr);

        $cartEntity->setItems($itemsArr);
        $em->flush();

        $this->addFlash('success', 'Item claimed successfully.');
        return $this->redirectToRoute('cart_purchases');
    }

}