<?php

namespace SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\DelaiEnvoi;

class LoadDelaiEnvoiData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /* TODO : les libellés devront être exportés dans le dossier translations */
        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('0 jour');
        $delaiEnvoi->setNbJours(0);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-0', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('2 jours');
        $delaiEnvoi->setNbJours(2);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-2', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('3 jours');
        $delaiEnvoi->setNbJours(3);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-3', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('4 jours');
        $delaiEnvoi->setNbJours(4);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-4', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('5 jours');
        $delaiEnvoi->setNbJours(5);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-5', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('6 jours');
        $delaiEnvoi->setNbJours(6);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-6', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('7 jours');
        $delaiEnvoi->setNbJours(7);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-7', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('8 jours');
        $delaiEnvoi->setNbJours(8);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-8', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('9 jours');
        $delaiEnvoi->setNbJours(9);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-9', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('10 jours');
        $delaiEnvoi->setNbJours(10);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-10', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('11 jours');
        $delaiEnvoi->setNbJours(11);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-11', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('12 jours');
        $delaiEnvoi->setNbJours(12);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-12', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('13 jours');
        $delaiEnvoi->setNbJours(13);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-13', $delaiEnvoi);

        $delaiEnvoi = new DelaiEnvoi();
        $delaiEnvoi->setLibelle('14 jours');
        $delaiEnvoi->setNbJours(14);
        $manager->persist($delaiEnvoi);
        $this->addReference('DelaiEnvoi-14', $delaiEnvoi);

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
