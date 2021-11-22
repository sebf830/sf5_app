<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog-et-temoignages', name: 'blog')]
    public function index(CategoryRepository $categoryRepo, ArticleRepository $articleRepo): Response
    {
        $categories = $categoryRepo->findAll();
        $temoignage = $categoryRepo->findOneBy(['name' => 'temoignages']);

        $lastArticles = $articleRepo->findAll();
        return $this->render(
            'blog/index.html.twig',
            [
                'categories' => $categories,
                "temoignage" => $temoignage,
                "articles" => $lastArticles
            ]
        );
    }

    #[Route('/articles/{slug}', name: 'app_article_category')]
    public function showCategoryArticles(Category $category, PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate($category->getArticles(), $request->query->getInt('page', 1), 8);
        return $this->render('/blog/show_category_articles.html.twig', ['category' => $category, 'articles' => $articles]);
    }

    #[Route('/article/{slug}', name: 'app_show_article')]
    public function showArticle(Article $article, ArticleRepository $articleRepository, CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();
        $suggestions = $articleRepository->findBy(['category' => $article->getCategory()], [], 5, null);

        return $this->render('/blog/show_article.html.twig', ['article' => $article, "suggestions" => $suggestions, 'categories' => $categories]);
    }

    #[Route('/lire-les-{name}', name: 'app_show_stories')]
    public function showStories(Category $category, ArticleRepository $articleRepository): Response
    {
        $stories = $articleRepository->findBy(['category' => $category]);

        return $this->render('/blog/show_stories.html.twig', ['stories' => $stories]);
    }
}
