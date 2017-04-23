<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use WebStoreBundle\Form\ProfileType;

class ProfileController extends Controller
{
    /**
     * @Route("/profile", name="my_profile")
     */
    public function updateProfileAction(Request $request)
    {

        $usersRepo = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->redirectToRoute('index', array('message' => 'Please login.'));
        }

        $currentUserId = $currentUser->getId();

        $userEntity = $usersRepo->getRepository('WebStoreBundle:User')->find($currentUserId);
        $currentPass = $userEntity->getPassword();

        $form = $this->createForm(ProfileType::class, $userEntity);
        $form->handleRequest($request);


        $message = 'none';
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $newName = $formData->getFullName();
            if ($newName != '') {
                $userEntity->setFullName($newName);
            }

            $newPass = $formData->getPlainPassword();
            if ($newPass != '') {
                $encodedPass = $this
                    ->get('security.password_encoder')
                    ->encodePassword($userEntity, $newPass);
                $userEntity->setPassword($encodedPass);
            } else {
                $userEntity->setPassword($currentPass);
            }

            $newEmail = $formData->getEmail();
            if ($newEmail != '') {
                $userEntity->setEmail($newEmail);
            }

            $usersRepo->persist($userEntity);
            $usersRepo->flush();
            $usersRepo->refresh($userEntity);

            $message = 'success';

//            return $this->redirectToRoute('index');
        }
        return $this->render('default/my_profile.html.twig',
            array(
                'profile_form' => $form->createView(),
                'message' => $message
            ));
    }

    /**
     * @Route("/money", name="get_money")
     */
    public function getMoneyAction(Request $request)
    {
        $usersRepo = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->redirectToRoute('index', array('message' => 'Please login.'));
        }

        $currentUserId = $currentUser->getId();

        $userEntity = $usersRepo->getRepository('WebStoreBundle:User')->find($currentUserId);

        $newMoney = 100;

        $userEntity->addMoney($newMoney);

        $session = new Session();
        $session->start();
        $session->set('money', $userEntity->getMoney());

        $usersRepo->flush();

        return $this->redirectToRoute('index');

//        return $this->render('default/my_profile.html.twig',
//            array(
//                'profile_form' => $form->createView(),
//                'message' => $message
//            ));
    }
}
