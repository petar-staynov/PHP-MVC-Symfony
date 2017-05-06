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
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @param Request $request
     * @return Response
     */
    public function addCategoryAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category added successfully');
            return $this->redirectToRoute('admin_categories_panel');
        }
        return $this->render('administration/admin_category_add.html.twig', array(
            'category_add_form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/categories/view/{id}", name="admin_category_view")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
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
            $this->addFlash('danger', 'This category doesn\'t exist.');
            return $this->redirectToRoute('admin_categories_panel');
        }

        $categoryItems =
            $this
                ->getDoctrine()
                ->getRepository(Item::class)
                ->findBy(['category' => $id]);

        return $this->render('administration/admin_category_view.html.twig', array(
            'category' => $category,
            'items' => $categoryItems,
        ));
    }

    /**
     * @Route("/admin/categories/edit/{id}", name="admin_category_edit")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editCategoryAction($id, Request $request)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($category === null) {
            $this->addFlash('danger', 'The category doesn\'t exist');
            return $this->redirectToRoute('admin_categories_panel');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'The category was edited successfully');
            return $this->redirectToRoute('admin_categories_panel');
        }

        return $this->render('administration/admin_category_edit.html.twig', array(
            'category' => $category,
            'category_edit_form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/categories/delete/{id}", name="admin_category_delete")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_EDITOR')")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function deleteCategoryAction($id, Request $request)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category === null) {
            $this->addFlash('danger', 'The category doesn\'t exist');
            return $this->redirectToRoute('admin_categories_panel');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryItems = $this
                ->getDoctrine()
                ->getRepository(Item::class)
                ->findBy(['category' => $id]);

            foreach ($categoryItems as $item)
            {
                $em = $this->getDoctrine()->getManager();
                $em->remove($item);
                $em->flush();
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'The category was deleted successfully');
            return $this->redirectToRoute('admin_categories_panel');
        }

        return $this->render('administration/admin_category_delete.html.twig', array(
            'category' => $category,
            'category_edit_form' => $form->createView(),
        ));
    }
}
