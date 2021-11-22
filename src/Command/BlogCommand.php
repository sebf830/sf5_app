<?php

namespace App\Command;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:blog',
    description: 'Ajoute des ARTICLES en base de donnée',
)]
class BlogCommand extends Command
{

    private SymfonyStyle $io;
    private EntityManagerInterface $em;
    private ArticleRepository $articleRepository;

    public function __construct(
        EntityManagerInterface $em,
        ArticleRepository $articleRepository,
    ) {
        parent::__construct();
        $this->em = $em;
        $this->articleRepository = $articleRepository;
        $this->faker = Factory::create('fr_FR');
    }

    protected function configure(): void
    {
        $this
            ->addArgument('number', InputArgument::REQUIRED, 'Combien d\'articles souhaitez vous insérer ?');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->section("Ajouter des articles en bdd");
        $this->enterNumber($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $number = $input->getArgument('number');
        $users = $this->getUsers();

        $this->io->section('Purge des tables : Category, Article et Images');

        $this->em->getConnection()->query(
            'SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE category;
            TRUNCATE TABLE article;
            TRUNCATE TABLE image;
            SET FOREIGN_KEY_CHECKS = 1;'
        );


        $categories = ['Santé et protection animal', 'conseils', 'temoignages', 'engagement', 'adoption'];

        for ($i = 0; $i < count($categories); $i++) {
            $category = (new Category())
                ->setName($categories[$i])
                ->setImage("https://loremflickr.com/640/420/animal?random={$i}")
                ->setSlug(str_replace(' ', '-', strtolower($categories[$i])))
                ->setDescription($this->faker->realTextBetween(100, 200));
            $this->em->persist($category);

            for ($j = 0; $j < $number; $j++) {

                $article = new Article();
                $article->setTitle($this->faker->realTextBetween(10, 20));
                $article->setDate($this->faker->dateTimeBetween('-20 week', '+1 week'));
                $article->setAuthor($users[$this->faker->numberBetween(0, count($users) - 1)]);
                $article->setCategory($category);
                if ($category->getName() == 'temoignages') {
                    $article->setContent($this->faker->realTextBetween(300, 900));
                } else {
                    $article->setContent(
                        '<p>' . $this->faker->realTextBetween(200, 400) . '</p>
                        <h5>' . $this->faker->realTextBetween(20, 50) . '</h5>
                        <p>' . $this->faker->realTextBetween(400, 700) . '</p>
                        <h5>' . $this->faker->realTextBetween(20, 50) . '</h5>
                        <p>' . $this->faker->realTextBetween(400, 700) . '</p>'
                    );
                }
                $this->em->persist($article);



                for ($k = 0; $k < 3; $k++) {
                    $image = (new Image())
                        ->setName("https://loremflickr.com/640/420/dogs?random={$this->faker->numberBetween(0, 200)}");

                    $this->em->persist($image);

                    $article->addImage($image);
                    $check = ['!', '?', "'", '.', ';', ','];
                    $slug = str_replace(' ', '-', strtolower($article->getTitle()));
                    $slug = str_replace($check, '', $slug);
                    $article->setSlug($slug);

                    $this->em->persist($article);
                }
            }
        }
        $this->em->flush();

        $countArticles = count($this->articleRepository->findAll());

        $successMessage = $number > 1 ?
            "{$number} articles insérés en base de donnée - Total article(s) : {$countArticles}" :
            "{$number} article a été inséré en base de donnée - Total article(s) : {$countArticles}";

        $this->io->section('');
        $this->io->success($successMessage);

        return Command::SUCCESS;
    }

    public function enterNumber(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $NumberQuestion = new Question("Combien d'articles souhaitez vous insérer ? ");
        $number = $helper->ask($input, $output, $NumberQuestion);

        $input->setArgument('number', $number);

        if (empty($input->getArgument('number'))  && !preg_match("/^[0-9]{1,3}$/", $input->getArgument('number'))) {
            $this->enterNumber($input, $output);
        } else {
            $this->io->info("Votre choix est pris en compte : {$number} article(s)");
        }
    }

    private function getUsers(): array
    {
        $users = $this->em->getRepository(User::class)->findAll();
        return $users;
    }
}
