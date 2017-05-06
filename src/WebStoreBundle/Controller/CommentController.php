<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use WebStoreBundle\Entity\Comment;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Entity\Category;
use WebStoreBundle\Form\CommentType;

class CommentController extends Controller
{
    /**
     * @Route("/item/{id}/comment/add", name="item_view")
     * @param $itemId
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function addCommentAction($itemId, Request $request)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->findBy($itemId);

        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setItem($item);

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success','Comment posted.');
            return $this->redirect($request->headers->get('referer'));
        }
    }
}
