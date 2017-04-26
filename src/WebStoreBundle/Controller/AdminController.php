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
        return self::securedRenderer($renderTemplate, null);
    }

    /**
     * @Route("/admin/categories", name="admin_edit_categories")
     * @param Request $request
     * @return Response
     */
    public function editCategories(Request $request)
    {
        $renderTemplate = 'administration/admin_categories_panel.html.twig';
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $renderParameters['categories'] = $categories;
        return self::securedRenderer($renderTemplate, $renderParameters);
    }


    /**
     * @Route("/admin/categories/view/{id}", name="admin_category_view")
     * @param $id
     * @return Response
     */
    public function viewCategory($id)
    {
        $renderTemplate = 'administration/admin_category_view.html.twig';
        $renderParameters = [];
        $category = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        $renderParameters['category'] = $category;

        $categoryItems =
            $this
                ->getDoctrine()
                ->getRepository(Item::class)
                ->findBy(['category' => $id]);
        $renderParameters['items'] = $categoryItems;

        return $this->securedRenderer($renderTemplate, $renderParameters);
    }

    /**
     * @Route("/admin/categories/edit/{id}", name="admin_category_edit")*
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editCategory($id, Request $request)
    {
        $renderTemplate = 'administration/admin_category_edit.html.twig';
        $renderParameters = [];
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($category === null) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_edit_categories');
        }
        $renderParameters['category'] = $category;
        $renderParameters['category_edit_form'] = $form->createView();

        return $this->securedRenderer($renderTemplate, $renderParameters);
    }

    /**
     * @Route("/admin/categories/delete/{id}", name="admin_category_delete")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function deleteItem($id, Request $request)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $renderTemplate = 'administration/admin_category_delete.html.twig';
        $renderParameters = [];

        if ($category === null) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            return $this->redirectToRoute('admin_edit_categories');
        }
        $renderParameters['category'] = $category;
        $renderParameters['category_edit_form'] = $form->createView();
        return $this->securedRenderer($renderTemplate, $renderParameters);
    }

    /**
     * @param $renderTemplate
     * @param $renderParameters
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
