<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    /**
     * @Route("/{category<\d+>?}", name="articles")
     */
    public function articles(ArticleRepository $articleRep, CategoryRepository $categoryRep, ? Category $category): Response
    {
        if ($category)
            $articles = $category->getArticles()->getValues();
        else
            $articles = $articleRep->findAll();
        $categories = $categoryRep->findAll();

        return $this->render(
            'article/articles.html.twig',
            [
                'category' => $category,
                'categories' => $categories,
                'articles' => $articles,
            ]
        );
    }

    /**
     * @Route("/{article<\d+>}", name="article")
     */
    public function article(? Article $article): Response
    {
        return $this->render(
            'article/article.html.twig',
            [
                'article' => $article,
            ]
        );
    }

    /**
     * @Route("/article/create", name="create_article")
     */
    public function create_article(Request $request, EntityManagerInterface $manager) : Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute("articles");
        }

        return $this->render(
            "/article/new.html.twig",
            [
                'form' => $form->createView(),
            ]
        );
    }
}
