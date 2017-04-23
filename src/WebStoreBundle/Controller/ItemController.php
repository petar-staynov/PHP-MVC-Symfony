<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Form\ItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ItemController extends Controller
{
    /**
     * @Route("/add_item", name="add_item")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function addItem(Request $request)
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){
            $data = $form->getData();

            $currentUser = $this->getUser();
            $item->setOwner($currentUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('items/item_add.html.twig',
            array(
                'item_form' => $form->createView(),
            ));
    }

    /**
     * @Route("/item/{id}", name="item_view")
     * @param $id
     * @return Response
     */
    public function viewItem($id)
    {
        $item = $this
            ->getDoctrine()
            ->getRepository(Item::class)
            ->find($id);

        $dateObj = $item->getDateAdded();

        return $this->render('items/item_view.html.twig', array(
            'item' => $item,
            'addedDate' => $dateObj->format('d-m-y'),
        ));
    }

    /**
     * @Route("/item/edit/{id}", name="item_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editItem($id, Request $request)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        if($item === null)
        {
            return $this->redirectToRoute('index');
        }
        $currentUser = $this->getUser();
        if(!$currentUser->isOwner($item) && !$currentUser->isAdmin())
        {
            return $this->redirectToRoute("index");
        }


        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('item_view', array(
                'id' => $item->getId()
            ));
        }

        return $this->render('items/item_edit.html.twig', array(
            'item' => $item, 'item_edit_form' => $form->createView()
        ));
    }

    /**
     * @Route("/item/delete/{id}", name="item_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function deleteItem($id, Request $request)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        if($item === null)
        {
            return $this->redirectToRoute('index');
        }
        $currentUser = $this->getUser();
        if(!$currentUser->isOwner($item) && !$currentUser->isAdmin())
        {
            return $this->redirectToRoute("index");
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

            return $this->redirectToRoute('index');
        }
        return $this->render('items/item_delete.html.twig',
            array('article' => $item,
                'item_delete_form' => $form->createView()));
    }
}
