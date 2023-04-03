<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        /* Check if the current user who wants to edit profile if he is the same user who has access */
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
                if ($request->request->get('newPassword') != ''){
                    $user->setPassword($passwordEncoder->encodePassword(
                        $user,
                        $request->request->get('newPassword')
                    ));
                }
                $userRepository->add($user);
                return $this->redirectToRoute('app_home',[
                    'success' => true,
                    'message' => "Profile changed successfully"
                ]);
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'error' => $error,
                'message' => "Username exists"
            ]);
        }else{

            return $this->redirectToRoute('app_home',[
                'success' => false,
                'message' => ""
            ]);
        }

    }

}
