<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use WebStoreBundle\Entity\Comment;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Entity\Category;
use WebStoreBundle\Form\CommentType;

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
     * @param Request $request
     * @return Response
     */
    public function viewItemAction($id, Request $request)
    {
        $item = $this
            ->getDoctrine()
            ->getRepository(Item::class)
            ->find($id);

        if ($item === null) {
            $this->addFlash('danger', 'This item doesn\'t exist');
            $this->redirectToRoute('index');
        }

        $itemComments = $this
            ->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(array('itemId' => $id));

        if ($this->getUser() !== null) {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setAuthor($this->getUser());
                $comment->setAuthorId($this->getUser()->getId());
                $comment->setItem($item);
                $comment->setItemId($item->getId());

                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Comment posted.');
                return $this->redirect($request->headers->get('referer'));
            }
            return $this->render('items/item_view.html.twig', array(
                'item' => $item,
                'itemComments' => $itemComments,
                'comment_form' => $form->createView(),
            ));
        }

        return $this->render('items/item_view.html.twig', array(
            'item' => $item,
            'itemComments' => $itemComments,
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

        if ($category === null) {
            $this->addFlash('danger', 'This category doesn\'t exist');
            $this->redirectToRoute('index');
        }

        $categoryItems =
            $this
                ->getDoctrine()
                ->getRepository(Item::class)
                ->findBy(['category' => $id]);

        return $this->render('categories/category_view.html.twig', array(
            'category' => $category,
            'categoryItems' => $categoryItems,
        ));
    }
}
