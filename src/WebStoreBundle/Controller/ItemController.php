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
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @param Request $request
     * @return Response
     */
    public function addItemAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            $item->setOwner($currentUser);
            if($item->getDiscounted() == true &&
                $item->getDiscount() <= 0 ||
                $item->getDiscountExpirationDate() <= new \DateTime('now')){


                $this->addFlash('danger', 'Discount too low or discount date is invalid.');
                return $this->redirect($request->headers->get('referer'));
            }
            $em->persist($item);
            $em->flush();

            $this->addFlash('success', 'Item added successfully.');
            return $this->redirectToRoute('admin_items_panel');
        }

        return $this->render('administration/admin_item_add.html.twig',
            array(
                'item_form' => $form->createView(),
            ));
    }


    /**
     * @Route("/admin/item/edit/{id}", name="admin_item_edit")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editItemAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        if ($item === null) {
            $this->addFlash('danger', 'This item doesn\'t exist.');
            return $this->redirectToRoute('admin_items_panel');
        }
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($item) && !$currentUser->isEditor()) {
            return $this->redirectToRoute("index");
        }


        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($item->getDiscounted() == true &&
                $item->getDiscount() <= 0 ||
                $item->getDiscountExpirationDate() <= new \DateTime('now')){
                
                $this->addFlash('danger', 'Discount too low or discount date is invalid.');
                return $this->redirect($request->headers->get('referer'));
            }

            $em->flush();

            $this->addFlash('success', 'Item edited successfully.');
            return $this->redirect($request->headers->get('referer'));

        }

        return $this->render('administration/admin_item_edit.html.twig', array(
            'item' => $item,
            'item_edit_form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/item/delete/{id}", name="admin_item_delete")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function deleteItemAction($id, Request $request)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        if ($item === null) {
            $this->addFlash('danger', 'This item doesn\'t exist.');
            return $this->redirectToRoute('admin_items_panel');
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

            $this->addFlash('success', 'Item deleted successfully.');
            return $this->redirectToRoute('admin_items_panel');
        }
        return $this->render('administration/admin_item_delete.html.twig',
            array('article' => $item,
                'item_delete_form' => $form->createView()));
    }
}
