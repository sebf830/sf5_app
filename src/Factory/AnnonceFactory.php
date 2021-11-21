<?php

namespace App\Factory;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Animal;
use DateTimeImmutable;
use App\Entity\Annonce;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceFactory extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->faker = Factory::create('fr_FR');
    }

    public function makeAnnonce(array $data, User $user, $type)
    {
        $animal = (new Animal())
            ->setType($type)
            ->setName($data['name'] ?? null)
            ->setGender($data['gender'])
            ->setAge($data['age'] ?? null)
            ->setColor($data['color'])
            ->setIsLost($data['isLost'])
            ->setUser($user)
            ->setPuce($data['puce'] ?? null)
            ->setRace($data['race']);
        $this->em->persist($animal);

        $annonce = (new Annonce())
            ->setUser($user)
            ->setAnimal($animal)
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setImage($data['image'] ?? null)
            ->setLostAt($data['lostAt'] ?? null)
            ->setFoundAt($data['foundAt'] ?? null)
            ->setLocation($data['location'])
            ->setNumero($this->faker->regexify('[A-Z]{5}[0-4]{5}'))
            ->setCreatedAt(new DateTimeImmutable());
        $this->em->persist($annonce);

        $image = $data['image'] ?? null;
        if ($image != null) {
            $fichier = md5(uniqid()) . '.' . $image->guessExtension();
            $image->move($this->getParameter('uploads_directory'), $fichier);
            $annonce->setImage($fichier);
        } else {
            $annonce->setImage('default_animal.png');
        }
        $this->em->persist($annonce);

        $this->em->flush();
    }

    public function convert(string $date): DateTime
    {
        $dat = new DateTime();
        return $dat->createFromFormat("Y-m-d H:i:s", $date);
    }
}
