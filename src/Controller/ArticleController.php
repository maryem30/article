<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        return $this->render('article/index.html.twig', [
            /* List only articles per user */
            'articles' => $articleRepository->findBy(['createdBy' => $this->get('security.token_storage')->getToken()->getUser(), 'deletedAt' => null]), 'success' => $request->query->get('success'), 'message' => $request->query->get('message')
        ]);
    }

    /**
     * @Route("/new", name="app_article_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*Add the user who created the article*/
            $article->setCreatedBy($this->get('security.token_storage')->getToken()->getUser());
            $articleRepository->add($article);
            return $this->redirectToRoute('app_article_index', ['success' => true, 'message' => 'Article created successfully'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        /* Check if the current user who wants to access if he is the same user who has access */
        if ($article->getCreatedBy() == $this->get('security.token_storage')->getToken()->getUser()) {

            return $this->render('article/show.html.twig', [
                'article' => $article,
            ]);
        } else {

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}/edit", name="app_article_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        /* Check if the current user who wants to access if he is the same user who has access */
        if ($article->getCreatedBy() == $this->get('security.token_storage')->getToken()->getUser()) {
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $articleRepository->add($article);

                return $this->redirectToRoute('app_article_index', ['success' => true, 'message' => 'Article changed successfully'], Response::HTTP_SEE_OTHER);
            }

            return $this->render('article/edit.html.twig', [
                'article' => $article,
                'form' => $form->createView(),
            ]);
        } else {

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}", name="app_article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        /* Check if the current user who wants to access if he is the same user who has access */
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token')) && $article->getCreatedBy() == $this->get('security.token_storage')->getToken()->getUser()) {
            $article->setDeletedAt(new  \DateTimeImmutable());
            $this->getDoctrine()->getManager()->flush();

        }

        return $this->redirectToRoute('app_article_index', ['success' => true, 'message' => 'Article deleted successfully'], Response::HTTP_SEE_OTHER);
    }
}
