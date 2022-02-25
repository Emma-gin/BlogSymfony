<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Articles;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 100; $i++){
            $article = new Articles();
            $article->setTitle('Titre ' .$i);
            $article->setPicture('https://via.placeholder.com/150');
            $article->setContent('content lorem ipsum ' .$i);
            $article->setPublicationDate(new \DateTime());
            $article->setLastUpdateDate(new \DateTime());
            $article->setIsPublished(true);

            $manager->persist($article);
        }

        $manager->flush();
    }
}
