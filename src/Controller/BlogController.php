<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\StoryType;
use App\Entity\Category;
use App\Factory\ArticleBlogFactory;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

    private PaginatorInterface $paginator;
    private ArticleRepository $articleRepository;
    private FlashyNotifier $flash;

    public function __construct(PaginatorInterface $paginator, ArticleRepository $articleRepository, FlashyNotifier $flash)
    {
        $this->paginator = $paginator;
        $this->articleRepository = $articleRepository;
        $this->flash = $flash;
    }

    #[Route('/blog-et-temoignages', name: 'blog')]
    public function index(CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();
        $temoignage = $categoryRepo->findOneBy(['name' => 'temoignages']);

        $lastArticles = $this->articleRepository->findBy(['publication_status' => 1]);
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
    public function showCategoryArticles(Category $category, Request $request): Response
    {
        $articles = $this->paginator->paginate($category->getArticles(), $request->query->getInt('page', 1), 8);
        return $this->render('/blog/show_category_articles.html.twig', ['category' => $category, 'articles' => $articles]);
    }

    #[Route('/article/{slug}', name: 'app_show_article')]
    public function showArticle(Article $article, CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();
        $suggestions = $this->articleRepository->findBy(['category' => $article->getCategory(), 'publication_status' => 1], [], 5, null);

        return $this->render('/blog/show_article.html.twig', ['article' => $article, "suggestions" => $suggestions, 'categories' => $categories]);
    }

    #[Route('/lire-les-{name}', name: 'app_show_stories')]
    public function showStories(Category $category, Request $request): Response
    {
        $stories = $this->paginator->paginate($this->articleRepository->findBy(['category' => $category, 'publication_status' => 1]), $request->query->getInt('page', 1), 8);
        $suggestions = $this->articleRepository->findBy([], [], 5, null);
        return $this->render('/blog/show_stories.html.twig', ['stories' => $stories, "suggestions" => $suggestions]);
    }

    #[Route('/publier-des-{name}', name: 'app_publish_story')]
    public function publishStories(Category $category, Request $request, ArticleBlogFactory $articlefactory): Response
    {
        if (!$this->getUser()) {
            $this->flash->success('Connectez-vous pour accéder à cette section', '');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(StoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $articlefactory->makeTemoignage($data, $this->getUser(), $category);

            $this->flash->success('Votre story est enregistrée, elle sera validée dans un délai de 48H', '');
            return $this->redirectToRoute('blog');
        }

        $suggestions = $this->articleRepository->findBy(['publication_status' => 1], [], 5, null);
        return $this->render('/blog/publish_story.html.twig', ["suggestions" => $suggestions, 'form' => $form->createView()]);
    }
}
