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
use WebStoreBundle\Form\CategoryType;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $renderTemplate = 'administration/admin_panel.html.twig';
        return $this->securedRenderer($renderTemplate, null);
    }

    /**
     * @Route("/admin/items_panel", name="admin_items_panel")
     * @param Request $request
     * @return Response
     */
    public function itemsPanel(Request $request)
    {
        $renderTemplate = 'administration/admin_items_panel.html.twig';
        $renderParameters = [];

        $items = $this->getDoctrine()->getRepository(Item::class)->findAll();
        $renderParameters['items'] = $items;

        return $this->securedRenderer($renderTemplate, $renderParameters);
    }

    /**
     * @Route("/admin/categories_panel", name="admin_categories_panel")
     * @param Request $request
     * @return Response
     */
    public function categoriesPanel(Request $request)
    {
        $renderTemplate = 'administration/admin_categories_panel.html.twig';
        $renderParameters = [];

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $renderParameters['categories'] = $categories;

        return $this->securedRenderer($renderTemplate, $renderParameters);
    }

    /**
     * @param $renderTemplate
     * @param $renderParameters
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function securedRenderer($renderTemplate, $renderParameters)
    {
        //Checks if logged
        $currentUser = $this->getUser();
        if ($currentUser === null) {
            return $this->redirectToRoute('index');
        }

        //Check user role and send it to twig. If no special role, redirect to index
        $currentUserRoles = $currentUser->getRoles();
        if (in_array('ROLE_ADMIN', $currentUserRoles)) {
            $renderParameters['role'] = 'admin';
            return $this->render($renderTemplate, $renderParameters);
        } elseif (in_array('ROLE_EDITOR', $currentUserRoles)) {
            $renderParameters['role'] = 'editor';
            return $this->render($renderTemplate, $renderParameters);
        } else {
            return $this->redirectToRoute('index');
        }
    }
}
