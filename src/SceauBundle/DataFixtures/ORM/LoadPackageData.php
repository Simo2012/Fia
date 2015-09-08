<?php

namespace SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\Package;

class LoadPackageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $package = new Package();
        $package->setLibelle('Initial');
        $package->setActif(true);
        $manager->persist($package);
        $this->addReference('Package-1', $package);
        
        $package = new Package();
        $package->setLibelle('Essentiel');
        $package->setActif(true);
        $manager->persist($package);
        $this->addReference('Package-2', $package);

        $package = new Package();
        $package->setLibelle('Intégral');
        $package->setActif(true);
        $manager->persist($package);
        $this->addReference('Package-3', $package);
        
        $package = new Package();
        $package->setLibelle('Fidélité (Ancien payant)');
        $package->setActif(true);
        $manager->persist($package);
        $this->addReference('Package-4', $package);
        
        $package = new Package();
        $package->setLibelle('Historique (Ancien gratuit)');
        $package->setActif(true);
        $manager->persist($package);
        $this->addReference('Package-5', $package);
        
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
