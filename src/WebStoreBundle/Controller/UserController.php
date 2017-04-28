<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use WebStoreBundle\Entity\User;
use WebStoreBundle\Form\UserType;
use WebStoreBundle\Entity\Role;

class UserController extends Controller
{
    /**
     * @Route("/admin/user/add", name="admin_user_add")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function addUserAction(Request $request)
    {
        $renderTemplate = 'administration/admin_user_add.html.twig';
        $renderParameters = [];

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $renderParameters = ['admin_register_form' => $form->createView()];

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this
                ->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $roleRepo = $this->getDoctrine()->getRepository(Role::class);
            $userRole = $roleRepo->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_users_panel');
        }
        return $this->securedRenderer($renderTemplate, $renderParameters);
    }


    /**
     * @Route("/admin/user/edit/{id}", name="admin_user_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editUserAction($id, Request $request)
    {
        $renderTemplate = [];
        $renderParameters = [];

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_users_panel');
        }

        return $this->render('administration/admin_user_edit.html.twig', array(
            'user' => $user, 'user_edit_form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function deleteUserAction($id, Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_users_panel');
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
