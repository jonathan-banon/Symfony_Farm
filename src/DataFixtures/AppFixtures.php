<?php


namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Type;
use App\Entity\Breed;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer des types
        $types = ['Chien', 'Chat', 'Cheval'];
        $typeObjects = [];

        foreach ($types as $typeName) {
            $type = new Type();
            $type->setName($typeName);
            $manager->persist($type);
            $typeObjects[] = $type;
        }

        // Créer des races pour chaque type
        $breeds = [
            'Chien' => ['Labrador', 'Golden Retriever', 'Bulldog'],
            'Chat' => ['Siamois', 'Persan', 'Maine Coon'],
            'Cheval' => ['Frison', 'Pottok', 'Irish Cob']
        ];

        $breedObjects = [];

        foreach ($breeds as $typeName => $breedNames) {
            $type = array_filter($typeObjects, fn($t) => $t->getName() === $typeName);
            $type = reset($type);

            foreach ($breedNames as $breedName) {
                $breed = new Breed();
                $breed->setName($breedName);
                $breed->setType($type);
                $manager->persist($breed);
                $breedObjects[] = $breed;
            }
        }

        // Créer des animaux avec des types et des races associées
        for ($i = 0; $i < 9; $i++) {
            $animal = new Animal();
            $animal->setName($faker->name);
            $animal->setPrice($faker->randomFloat(2, 10, 500));
            $animal->setAge($faker->numberBetween(1, 15)); // Âge entre 1 et 15
            $animal->setDescription($faker->sentence(10));

            // Attribuer un type aléatoire
            $type = $typeObjects[array_rand($typeObjects)];
            $animal->setType($type);

            // Attribuer une race aléatoire
            if ($type->getName() !== 'Oiseau') {
                $breed = $breedObjects[array_rand($breedObjects)];
                // Vous pouvez associer un `breed` si nécessaire
            }

            $manager->persist($animal);
        }

        $manager->flush();
    }
}
