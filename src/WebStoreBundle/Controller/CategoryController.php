<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use WebStoreBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use WebStoreBundle\Entity\Item;
use WebStoreBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CategoryController extends Controller
{
    /**
     * @Route("/admin/categories/add", name="admin_add_category")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function addCategoryAction(Request $request)
    {
        $renderTemplate = 'administration/admin_category_add.html.twig';
        $renderParameters = [];

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('admin_categories_panel');
        }
        $renderParameters['category_add_form'] = $form->createView();
        return $this->securedRenderer($renderTemplate, $renderParameters);
    }

    /**
     * @Route("/admin/categories/view/{id}", name="admin_category_view")
     * @param $id
     * @return Response
     */
    public function viewCategoryAction($id)
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
    public function editCategoryAction($id, Request $request)
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

            return $this->redirectToRoute('admin_categories_panel');
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
    public function deleteCategoryAction($id, Request $request)
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

            return $this->redirectToRoute('admin_categories_panel');
        }
        $renderParameters['category'] = $category;
        $renderParameters['category_edit_form'] = $form->createView();
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
