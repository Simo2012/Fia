<?php

namespace SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\ArticlePresse;

class LoadArticlePresseData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /* TODO : les libellés devront être exportés dans le dossier translations */

        for($i=0; $i<50; $i++)
        {
            $articlePresse = new ArticlePresse();
            $articlePresse->setTitle("Titre $i");
            $articlePresse->setDate(new \DateTime());
            $articlePresse->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
            );
            $articlePresse->setSource('SOURCE $i');
            $articlePresse->setUrlSource('URL SOURCE $i');
            $articlePresse->setPublished(true);

            $manager->persist($articlePresse);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
