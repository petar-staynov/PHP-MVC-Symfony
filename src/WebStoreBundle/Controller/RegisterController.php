<?php

namespace WebStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        //TODO Products service check

        if($this->getUser()){
            $this->addFlash('danger','You are already registered.');
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this
                ->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $userRole = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);

            $em = $this->getDoctrine()->getManager();
            try{
                $em->persist($user);
                $em->flush();
            }catch(\Exception $e){
                $this->addFlash('danger','This username and/or email is taken');
                return $this->redirectToRoute('user_register');
            }
            $this->addFlash('success','You have successfully registered');
            return $this->redirectToRoute('security_login');
        }
        return $this->render('default/register.html.twig',
            array(
                'register_form' => $form->createView(),
            ));
    }
}