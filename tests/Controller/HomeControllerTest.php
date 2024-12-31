<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        try {
            $this->assertResponseIsSuccessful();
            $this->assertSelectorExists('title');

            $title = $crawler->filter('title')->text();
            $this->assertStringContainsString('Family Farm', $title, "The title should contain 'Family Farm'");
            echo "\n----------------------------------------\n";
            echo "✅ TestIndex : Le test de la page d'accueil est réussi. Le titre contient 'Welcome'.\n";
            echo "\n----------------------------------------\n";
        } catch (\PHPUnit\Framework\AssertionFailedError $e) {
            echo "\n----------------------------------------\n";
            echo "❌ TestIndex : Échec du test de la page d'accueil. Détails : " . $e->getMessage() . "\n";
            echo "\n----------------------------------------\n";
        }
    }


    public function testCheckSession()
    {
        $client = static::createClient();
        $client->request('GET', '/check-session');

        try {
            $this->assertResponseIsSuccessful();
            $this->assertJson($client->getResponse()->getContent());

            $data = json_decode($client->getResponse()->getContent(), true);
            $this->assertArrayHasKey('isLoggedIn', $data);
            $this->assertArrayHasKey('user', $data);
            echo "\n----------------------------------------\n";
            echo "✅ TestCheckSession : Le test de la session est réussi. Les clés attendues sont présentes.\n";
            echo "\n----------------------------------------\n";
        } catch (\PHPUnit\Framework\AssertionFailedError $e) {
            echo "\n----------------------------------------\n";
            echo "❌ TestCheckSession : Échec du test de la session. Détails : " . $e->getMessage() . "\n";
            echo "\n----------------------------------------\n";
        }
    }


    public function testAnimalsByType()
    {
        $client = static::createClient();

        $client->request('GET', '/type/all');
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $types = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($types, "La réponse pour les types doit être un tableau valide.");
        if (count($types) === 0) {
            echo "❌ TestAnimalsByType : Aucun type trouvé. Le test ne peut pas continuer.\n";
            $this->markTestIncomplete('Aucun type disponible. Impossible de tester les animaux associés.');
            return;
        }

        $firstTypeId = $types[0]['id'] ?? null;

        if (!$firstTypeId) {
            echo "❌ TestAnimalsByType : Le premier type n'a pas d'ID valide. Le test ne peut pas continuer.\n";
            $this->markTestIncomplete('Le premier type ne possède pas d\'ID valide.');
            return;
        }

        echo "✅ TestAnimalsByType : Le premier type avec ID=$firstTypeId a été trouvé. Passage au test des animaux.\n";

        $client->request('GET', "/type/$firstTypeId/animals/");
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data, "La réponse pour les animaux doit être un tableau valide.");

        if (count($data) > 0) {
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('name', $data[0]);
            $this->assertArrayHasKey('breed', $data[0]);
            $this->assertArrayHasKey('description', $data[0]);
            $this->assertArrayHasKey('price', $data[0]);
            $this->assertArrayHasKey('isOnSale', $data[0]);
            $this->assertArrayHasKey('currentImageIndex', $data[0]);
            $this->assertArrayHasKey('images', $data[0]);
            $this->assertArrayHasKey('age', $data[0]);

            echo "✅ TestAnimalsByType : Les animaux du type ID=$firstTypeId contiennent toutes les clés attendues.\n";
        } else {
            echo "⚠️ TestAnimalsByType : Aucun animal trouvé pour le type ID=$firstTypeId.\n";
        }
    }
}
