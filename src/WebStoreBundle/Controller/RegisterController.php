<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WebStoreBundle\Entity\Role;
use WebStoreBundle\Entity\User;
use WebStoreBundle\Form\UserType;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        var_dump($form->isSubmitted());
        var_dump($form->isValid());

        $message = 'none';
        if ($form->isSubmitted() && $form->isValid()) {
            $message = 'success';

            $password = $this
                ->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $roleRepo = $this->getDoctrine()->getRepository(Role::class);
            $userRole = $roleRepo->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);

            var_dump($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return $this->render('default/register.html.twig',
            array(
                'register_form' => $form->createView(),
                'message' => $message
            ));
    }
}
