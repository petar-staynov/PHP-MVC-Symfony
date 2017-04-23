<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use WebStoreBundle\Entity\Item;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="categories")
     */
    public function indexAction(Request $request)
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
    public function viewCategory($id)
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

        return $this->render('categories/categoryView.html.twig', array(
            'category' => $category,
            'categoryItems'=> $categoryItems,
        ));
    }
}
