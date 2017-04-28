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
     * @Route("/admin/item/add", name="admin_item_add")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function addItemAction(Request $request)
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            $item->setOwner($currentUser);
            $item->setDiscounted();

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();


            return $this->redirectToRoute('admin_items_panel');
//            return $this->render('items/item_add.html.twig',
//                array(
//                    'item_form' => $form->createView(),
//                ));

        }

        return $this->render('administration/admin_item_add.html.twig',
            array(
                'item_form' => $form->createView(),
            ));
    }


    /**
     * @Route("/admin/item/edit/{id}", name="admin_item_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editItemAction($id, Request $request)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        if ($item === null) {
            return $this->redirectToRoute('index');
        }
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($item) && !$currentUser->isEditor()) {
            return $this->redirectToRoute("index");
        }


        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('item_view', array(
                'id' => $item->getId()
            ));
        }

        return $this->render('administration/admin_item_edit.html.twig', array(
            'item' => $item,
            'item_edit_form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/item/delete/{id}", name="admin_item_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function deleteItemAction($id, Request $request)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        if ($item === null) {
            return $this->redirectToRoute('index');
        }
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($item) && !$currentUser->isAdmin()) {
            return $this->redirectToRoute("index");
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

            return $this->redirectToRoute('admin_items_panel');
        }
        return $this->render('administration/admin_item_delete.html.twig',
            array('article' => $item,
                'item_delete_form' => $form->createView()));
    }
}
