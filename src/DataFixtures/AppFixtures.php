<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Animal;
use DateTimeImmutable;
use App\Entity\Annonce;
use App\Service\LocalGouvApi;
use App\Service\RandomUserApi;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Serializer\csv\AnimalRaceSerializer;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private RandomUserApi $randomUser;
    private LocalGouvApi $gouvApi;
    private AnimalRaceSerializer $raceSerializer;
    private UserPasswordHasherInterface $hasher;

    public function __construct(RandomUserApi $randomUser, LocalGouvApi $gouvApi, AnimalRaceSerializer $raceSerializer, UserPasswordHasherInterface $hasher)
    {
        $this->randomUser = $randomUser;
        $this->gouvApi = $gouvApi;
        $this->faker = Factory::create('fr_FR');
        $this->raceSerializer = $raceSerializer;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $type = ['chien', 'chat'];
        $gender = ['male', 'femelle'];
        $color = ['brun', 'blanc', 'creme',  'gris', 'roux', 'marron clair', 'tricolore', 'bicolore'];

        $users = $this->randomUser->getRandomUsers(50);
        $cities = $this->gouvApi->getAllIleDeFranceCities();

        for ($i = 0; $i < count($users); $i++) {
            $user = new User();
            $user->setFirstname($users[$i]['name']['first']);
            $user->setLastname($users[$i]['name']['last']);
            $user->setEmail($users[$i]['email']);
            $user->setCity($cities[rand(0, count($cities) - 1)]);
            $user->setRoles(['ROLE_USER']);
            $user->setPhone('0' . $this->faker->regexify('[0-4]{9}'));
            $user->setPassword($this->hasher->hashPassword($user, $users[$i]['login']['password']));
            $user->setAvatar($users[$i]['picture']['thumbnail']);
            $manager->persist($user);

            if ($i > 15) {
                $animal = (new Animal())
                    ->setName($this->faker->word())
                    ->setGender($gender[$this->faker->numberBetween(0, 1)])
                    ->setType($type[$this->faker->numberBetween(0, 1)])
                    ->setAge($this->faker->numberBetween(5, 12))
                    ->setColor($color[$this->faker->numberBetween(0, 7)])
                    ->setIsLost($this->faker->numberBetween(0, 1))
                    ->setUser($user);
                $manager->persist($animal);

                // set race
                if ($animal->getType() == 0) {
                    $races = $this->raceSerializer->getDataFromFile('public/data/race_chien.csv');
                    $animal->setRace($races[$this->faker->numberBetween(0, count($races) - 1)]['race_chien']);
                } else {
                    $races = $this->raceSerializer->getDataFromFile('public/data/race_chat.csv');
                    $animal->setRace($races[$this->faker->numberBetween(0, count($races) - 1)]['race_chat']);
                }
                $manager->persist($animal);

                // set puce
                if ($animal->getIsLost() == 1) {
                    $animal->setPuce($this->faker->regexify('[A-Z]{5}[0-4]{5}'));
                }
                $manager->persist($animal);


                $lostDate = $animal->getIsLost() == 1 ? $this->faker->dateTimeBetween('-5 week', '+1 week') : null;
                $foundDate = $animal->getIsLost() == 0 ? $this->faker->dateTimeBetween('-5 week', '+1 week') : null;

                $annonce = (new Annonce())
                    ->setTitle($this->faker->realTextBetween(10, 20))
                    ->setDescription($this->faker->realTextBetween(100, 200))
                    ->setImage("https://loremflickr.com/640/420/{$animal->getType()}?random={$i}")
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setAnimal($animal)
                    ->setLocation($user->getCity())
                    ->setLostAt($lostDate)
                    ->setNumero($this->faker->regexify('[A-Z]{2}[0-4]{5}'))
                    ->setFoundAt($foundDate)
                    ->setUser($user);
                $manager->persist($annonce);
            }
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['fixtures_dev'];
    }
}
