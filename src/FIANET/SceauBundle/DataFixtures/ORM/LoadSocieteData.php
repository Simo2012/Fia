<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\Societe;

class LoadSocieteData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /* Cdiscount */
        $societe = new Societe();
        $societe->setLibelle('Cdiscount SA');
        $manager->persist($societe);
        $this->addReference('Societe-1', $societe);

        /* Maty */
        $societe = new Societe();
        $societe->setLibelle('MATY');
        $manager->persist($societe);
        $this->addReference('Societe-2', $societe);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 9;
    }
}
