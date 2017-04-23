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

        return $this->render('items/add_item.html.twig',
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

        return $this->render('items/itemView.html.twig', array(
            'item' => $item,
            'addedDate' => $dateObj->format('d-m-y'),
        ));
    }

    public function editItem()
    {

    }
    public function deleteItem()
    {

    }
}
