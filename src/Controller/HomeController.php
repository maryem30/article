<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    /**
     * @Route("/", methods="GET" , name="app_home")
     */
    public function home(Request $request)
    {
        $token = $this->get('security.token_storage')->getToken();

        if (!$token) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('home.html.twig', [
                'success' => $request->query->get('success') , 'message' => $request->query->get('message')]
            );
    }
}