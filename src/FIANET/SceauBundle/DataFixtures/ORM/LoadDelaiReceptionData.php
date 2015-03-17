<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\DelaiReception;

class LoadDelaiReceptionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /* TODO : les libellés devront être exportés dans le dossier translations */
        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Immédiatement');
        $delaiReception->setNbJours(0);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-0', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Aujourd\'hui');
        $delaiReception->setNbJours(0);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-0bis', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Demain');
        $delaiReception->setNbJours(1);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-1', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Après-Demain');
        $delaiReception->setNbJours(2);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-2', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 3 jours');
        $delaiReception->setNbJours(3);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-3', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 4 jours');
        $delaiReception->setNbJours(4);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-4', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 5 jours');
        $delaiReception->setNbJours(5);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-5', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 6 jours');
        $delaiReception->setNbJours(6);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-6', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 7 jours');
        $delaiReception->setNbJours(7);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-7', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 8 jours');
        $delaiReception->setNbJours(8);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-8', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 9 jours');
        $delaiReception->setNbJours(9);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-9', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 10 jours');
        $delaiReception->setNbJours(10);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-10', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 11 jours');
        $delaiReception->setNbJours(11);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-11', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 12 jours');
        $delaiReception->setNbJours(12);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-12', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 13 jours');
        $delaiReception->setNbJours(13);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-13', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 14 jours');
        $delaiReception->setNbJours(14);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-14', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 15 jours');
        $delaiReception->setNbJours(15);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-15', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 3 semaines');
        $delaiReception->setNbJours(21);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-21', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 4 semaines');
        $delaiReception->setNbJours(28);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-28', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 5 semaines');
        $delaiReception->setNbJours(35);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-35', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 6 semaines');
        $delaiReception->setNbJours(42);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-42', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 7 semaines');
        $delaiReception->setNbJours(49);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-49', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 2 mois');
        $delaiReception->setNbJours(61);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-2m', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 3 mois');
        $delaiReception->setNbJours(92);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-3m', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 4 mois');
        $delaiReception->setNbJours(122);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-4m', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 5 mois');
        $delaiReception->setNbJours(153);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-5m', $delaiReception);

        $delaiReception = new DelaiReception();
        $delaiReception->setLibelle('Dans 6 mois');
        $delaiReception->setNbJours(183);
        $manager->persist($delaiReception);
        $this->addReference('DelaiReception-6m', $delaiReception);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
