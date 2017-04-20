<?php

namespace WebStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

        $form = $this->createForm(ProfileType::class, $userEntity);
        $form->handleRequest($request);

        $message = 'none';
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $newName = $formData->getFullName();
            if ($newName != '') {
                $userEntity->setFullName($newName);
            }

            $newPass = $formData->getPassword();
            if ($newPass != '') {
                $encodedPass = $this
                    ->get('security.password_encoder')
                    ->encodePassword($userEntity, $newPass);
                $userEntity->setPassword($encodedPass);

                $userEntity->setPassword($encodedPass);
            }

            $newEmail = $formData->getEmail();
            if ($newEmail != '') {
                $userEntity->setEmail($newEmail);
            }
            $usersRepo->flush();

            $message = 'success';

//            return $this->redirectToRoute('my_profile');
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

        $usersRepo->flush();

        return $this->redirectToRoute('index');

//        return $this->render('default/my_profile.html.twig',
//            array(
//                'profile_form' => $form->createView(),
//                'message' => $message
//            ));
    }
}
