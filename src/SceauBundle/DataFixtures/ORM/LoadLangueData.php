<?php

namespace SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\Langue;

class LoadLangueData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $langue = new Langue();
        $langue->setCode('fr');
        $langue->setLibelle('Français');
        $manager->persist($langue);
        $this->addReference('langue-fr', $langue);

        $langue = new Langue();
        $langue->setCode('en');
        $langue->setLibelle('Anglais');
        $manager->persist($langue);
        $this->addReference('langue-en', $langue);

        $langue = new Langue();
        $langue->setCode('es');
        $langue->setLibelle('Espagnol');
        $manager->persist($langue);
        $this->addReference('langue-es', $langue);

        $langue = new Langue();
        $langue->setCode('de');
        $langue->setLibelle('Allemand');
        $manager->persist($langue);
        $this->addReference('langue-de', $langue);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 6;
    }
}
