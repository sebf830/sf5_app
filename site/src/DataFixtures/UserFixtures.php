<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\LocalGouvApi;
use App\Service\RandomUserApi;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    private RandomUserApi $randomUser;
    private LocalGouvApi $gouvApi;

    public function __construct(RandomUserApi $randomUser, LocalGouvApi $gouvApi)
    {
        $this->randomUser = $randomUser;
        $this->gouvApi = $gouvApi;
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->randomUser->getRandomUsers(40);
        $cities = $this->gouvApi->getAllIleDeFranceCities();

        for ($i = 0; $i < count($users); $i++) {
            $user = (new User())
                ->setFirstname($users[$i]['name']['first'])
                ->setLastname($users[$i]['name']['last'])
                ->setEmail($users[$i]['email'])
                ->setCity($cities[rand(0, count($cities))])
                ->setRoles(['ROLE_USER'])
                ->setPassword($users[$i]['login']['password']);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
