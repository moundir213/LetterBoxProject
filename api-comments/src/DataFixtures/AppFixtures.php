<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Comment;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $comment = new Comment();
            $comment->setAuthor('Utilisateur ' . $i);
            $comment->setContent('Ceci est le contenu du commentaire numéro ' . $i);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
