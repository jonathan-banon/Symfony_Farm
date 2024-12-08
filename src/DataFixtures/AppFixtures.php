<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Type;
use App\Entity\Breed;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private HasherUserPasswordHasherInterface $passwordHasher;

    public function __construct(HasherUserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $types = ['Chien', 'Chat', 'Cheval'];
        $typeObjects = [];

        foreach ($types as $typeName) {
            $type = new Type();
            $type->setName($typeName);
            $manager->persist($type);
            $typeObjects[$typeName] = $type;
        }

        $breeds = [
            'Chien' => ['Labrador', 'Golden Retriever', 'Bulldog'],
            'Chat' => ['Siamois', 'Persan', 'Maine Coon'],
            'Cheval' => ['Frison', 'Pottok', 'Irish Cob']
        ];

        $breedObjectsByType = [];

        foreach ($breeds as $typeName => $breedNames) {
            $type = $typeObjects[$typeName];

            foreach ($breedNames as $breedName) {
                $breed = new Breed();
                $breed->setName($breedName);
                $breed->setType($type);
                $manager->persist($breed);

                $breedObjectsByType[$typeName][] = $breed;
            }
        }

        $animalCountPerType = 3;
        foreach ($types as $typeName) {
            for ($i = 0; $i < $animalCountPerType; $i++) {
                $animal = new Animal();
                $animal->setName($faker->firstName);
                $animal->setPrice($faker->randomFloat(2, 10, 500));
                $animal->setAge($faker->numberBetween(1, 15));
                $animal->setDescription($faker->text(100));

                $type = $typeObjects[$typeName];
                $animal->setType($type);

                $breed = $faker->randomElement($breedObjectsByType[$typeName]);
                $animal->setBreed($breed);

                $manager->persist($animal);
            }
        }

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRoles(['ROLE_USER']);

        $encodedPassword = $this->passwordHasher->hashPassword($user, 'test');
        $user->setPassword($encodedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}
