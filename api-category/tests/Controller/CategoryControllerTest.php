<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CategoryControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/categories'); // Route mise à jour pour les catégories

        self::assertResponseIsSuccessful(); // Vérifie si la réponse est un succès (HTTP 200)
    }

    public function testCreate(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/categories', [
            'json' => ['name' => 'New Category'], // Données envoyées pour créer une catégorie
        ]);

        self::assertResponseIsSuccessful(); // Vérifie que la réponse est un succès
        self::assertResponseStatusCodeSame(201); // Vérifie que le statut HTTP est 201 (Created)
        self::assertJsonContains(['name' => 'New Category']); // Vérifie que la catégorie créée contient bien le nom
    }

    public function testUpdate(): void
    {
        $client = static::createClient();
        // Crée une nouvelle catégorie avant de la mettre à jour
        $client->request('POST', '/api/categories', [
            'json' => ['name' => 'Old Category'],
        ]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $categoryId = $data['id'];

        // Met à jour la catégorie
        $client->request('PUT', '/api/categories/' . $categoryId, [
            'json' => ['name' => 'Updated Category'],
        ]);

        self::assertResponseIsSuccessful(); // Vérifie que la réponse est un succès
        self::assertJsonContains(['name' => 'Updated Category']); // Vérifie que la catégorie a bien été mise à jour
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        // Crée une nouvelle catégorie avant de la supprimer
        $client->request('POST', '/api/categories', [
            'json' => ['name' => 'Category to delete'],
        ]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $categoryId = $data['id'];

        // Supprime la catégorie
        $client->request('DELETE', '/api/categories/' . $categoryId);

        self::assertResponseIsSuccessful(); // Vérifie que la réponse est un succès
        self::assertResponseStatusCodeSame(200); // Vérifie que le statut HTTP est 200 (OK)
    }
}
