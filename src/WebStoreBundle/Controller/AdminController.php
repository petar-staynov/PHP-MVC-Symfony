<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Category;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Entity\User;
use WebStoreBundle\Entity\Role;
use WebStoreBundle\Form\CategoryType;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('administration/admin_panel.html.twig');
    }

    /**
     * @Route("/admin/items_panel", name="admin_items_panel")
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @return Response
     */
    public function itemsPanelAction(Request $request)
    {
        $items = $this->getDoctrine()->getRepository(Item::class)->findAll();

        return $this->render('administration/admin_items_panel.html.twig', array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/admin/item/view/{id}", name="admin_item_view")
     * @param $id
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @return Response
     */
    public function viewItemAction($id)
    {
        $item = $this
            ->getDoctrine()
            ->getRepository(Item::class)
            ->find($id);

        return $this->render('administration/admin_item_view.html.twig', array(
            'item' => $item,
        ));
    }

    /**
     * @Route("/admin/categories_panel", name="admin_categories_panel")
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @return Response
     */
    public function categoriesPanelAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('administration/admin_categories_panel.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * @Route("/admin/users_panel", name="admin_users_panel")
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN')")
     * @return Response
     */
    public function usersPanelAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('administration/admin_users_panel.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Route("/admin/user/promote/{id}", name="admin_user_promote")
     * @param $id
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN')")
     * @return Response
     */
    public function userPromoteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $roleRepo = $this->getDoctrine()->getRepository(Role::class);
        $userRole = $roleRepo->findOneBy(['name' => 'ROLE_USER']);

        $user->addRole($userRole);
        $em->flush();

        $this->addFlash('success', 'User ' . $user->getUsername() . "has been promoted to editor");
        return $this->redirectToRoute('admin_users_panel');
    }
}
