<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{



    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        if( $user == $this->get('security.token_storage')->getToken()->getUser()){
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            $error = false;
            if ($form->isSubmitted() && $form->isValid()) {

                $found = $userRepository->findOneBy(['username' => $form->getData()->getUsername()]);
                if ($found &&$form->getData()->getId() != $found->getId() ){
                    $error =true;
                    return $this->render('user/edit.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => $error,
                        'message' => "Username exists"]);
                }
                $userRepository->add($user);
                return $this->redirectToRoute('app_home');
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'error' => $error,
                'message' => "Username exists"
            ]);
        }else{
            return $this->redirectToRoute('app_home');
        }

    }

}
