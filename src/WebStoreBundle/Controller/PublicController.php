<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use WebStoreBundle\Entity\Item;
use WebStoreBundle\Entity\Category;


class PublicController extends Controller
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

    /**
     * @Route("/item/{id}", name="item_view")
     * @param $id
     * @return Response
     */
    public function viewItemAction($id)
    {
        $item = $this
            ->getDoctrine()
            ->getRepository(Item::class)
            ->find($id);

        return $this->render('items/item_view.html.twig', array(
            'item' => $item,
        ));
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function viewCategoriesAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('categories/categories.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * @Route("/categories/{id}", name="category_view")
     * @param $id
     * @return Response
     */
    public function viewCategoryAction($id)
    {
        $category = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);

        $categoryItems =
            $this
                ->getDoctrine()
                ->getRepository(Item::class)
                ->findBy(['category' => $id]);

        return $this->render('categories/category_view.html.twig', array(
            'category' => $category,
            'categoryItems'=> $categoryItems,
        ));
    }
}
