<?php

namespace App\Command;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use App\Command\Validator\UserCommandValidator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'make:admin:user',
    description: 'create an admin user',
)]
class UserAdminCommand extends Command
{
    private SymfonyStyle $io;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;
    private UserRepository $userRepo;
    private UserCommandValidator $validator;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepo,
        UserCommandValidator $validator,
    ) {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;
        $this->userRepo = $userRepo;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('firstname', InputArgument::REQUIRED, 'Renseignez un prénom')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Renseignez un nom')
            ->addArgument('city', InputArgument::REQUIRED, 'Renseignez une ville')
            ->addArgument('phone', InputArgument::REQUIRED, 'Renseignez un numero de telephone')
            ->addArgument('email', InputArgument::REQUIRED, 'Renseignez une adresse email')
            ->addArgument('password', InputArgument::REQUIRED, 'Renseignez un password')
            ->addArgument('password_confirm', InputArgument::REQUIRED, 'Confirmer le password');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->section("Ajout d'un nouvel admin en base de donnée");
        $this->enterFirstname($input, $output);
        $this->enterLastname($input, $output);
        $this->enterCity($input, $output);
        $this->enterPhone($input, $output);
        $this->enterEmail($input, $output);
        $this->enterPassword($input, $output);
        $this->enterPasswordConfirm($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $city = $input->getArgument('city');
        $phone = $input->getArgument('phone');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $passwordConfirm = $input->getArgument('password_confirm');

        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setCity($city);
        $user->setPhone($phone);
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $passwordConfirm));
        $user->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $this->io->success("Nouvel admin inséré en base de donnée");

        return Command::SUCCESS;
    }



    public function enterEmail(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $emailQuestion = new Question("Email de l'utilisateur : ");
        $emailQuestion->setValidator([$this->validator, 'validateEmail']);
        $email = $helper->ask($input, $output, $emailQuestion);

        if ($this->isUserAlreadyExists($email)) {
            throw new RuntimeException(sprintf("UTILISATEUR : '%s' DEJA PRESENT EN BASE DE DONNEE", $email));
        }

        $this->io->info("Email de l'utilisateur pris en compte : {$email}");
        $input->setArgument('email', $email);
    }

    private function isUserAlreadyExists(string $email): ?User
    {
        return $this->userRepo->findOneBy(['email' => $email]);
    }

    public function enterPassword(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $passwordQuestion = new Question("Entrer un mot de passe utilisateur : ");
        $passwordQuestion->setValidator([$this->validator, 'validatePassword']);

        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);

        $this->io->info("Password utilisateur pris en compte");
        $input->setArgument('password', $password);
    }

    private function enterPasswordConfirm(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $passwordConfirmQuestion = new Question("Confirmer le mot de passe : ");

        //masquer la saisi du mdp et ne pas l'afficher
        $passwordConfirmQuestion->setHiddenFallback(false);
        $passwordConfirm = $helper->ask($input, $output, $passwordConfirmQuestion);

        $input->setArgument('password_confirm', $passwordConfirm);

        if ($input->getArgument('password') !== $input->getArgument('password_confirm')) {
            $this->io->error("Les mots de passes ne sont pas identiques");
            $this->enterPasswordConfirm($input, $output);
        }
        $this->io->info("Password correctement confirmé");
    }

    private function enterFirstname(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $firstnameQuestion = new Question('Entrer un prénom pour l\'utilisateur : ');
        $firstnameQuestion->setValidator([$this->validator, 'validateFirstname']);

        $prenom = $helper->ask($input, $output, $firstnameQuestion);
        // $output->writeln("Prenom utilisateur pris en compte : {$prenom}");
        $this->io->info("Nom utilisateur pris en compte : {$prenom}");
        $input->setArgument('firstname', $prenom);
    }

    private function enterLastname(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $lastnameQuestion = new Question('Entrer un nom pour l\'utilisateur : ');
        $nom = $helper->ask($input, $output, $lastnameQuestion);

        $this->io->info("Nom utilisateur pris en compte : {$nom}");
        // $output->writeln("Nom utilisateur pris en compte : {$nom}");
        $input->setArgument('lastname', $nom);
    }

    private function enterCity(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $cityQuestion = new Question('Entrer une ville  : ');
        $cityQuestion->setValidator([$this->validator, 'validateCity']);
        $city = $helper->ask($input, $output, $cityQuestion);

        $this->io->info("Ville prise en compte : {$city}");
        $input->setArgument('city', $city);
    }

    private function enterPhone(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $phoneQuestion = new Question('Entrer un numero de telephone  : ');
        $phoneQuestion->setValidator([$this->validator, 'validatePhone']);
        $phone = $helper->ask($input, $output, $phoneQuestion);

        $this->io->info("Téléphone pris en compte : {$phone}");
        // $output->writeln("Nom utilisateur pris en compte : {$nom}");
        $input->setArgument('phone', $phone);
    }
}
