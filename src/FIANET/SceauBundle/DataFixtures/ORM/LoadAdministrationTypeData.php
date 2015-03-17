<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\AdministrationType;

class LoadAdministrationTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $siteType = new AdministrationType();
        $siteType->setLibelle('Flux XML');
        $manager->persist($siteType);
        $this->addReference('AdministrationType-1', $siteType);

        $siteType = new AdministrationType();
        $siteType->setLibelle('Via Certissim');
        $manager->persist($siteType);
        $this->addReference('AdministrationType-2', $siteType);

        $siteType = new AdministrationType();
        $siteType->setLibelle('Lien direct');
        $manager->persist($siteType);
        $this->addReference('AdministrationType-3', $siteType);

        $siteType = new AdministrationType();
        $siteType->setLibelle('CSV automatique');
        $manager->persist($siteType);
        $this->addReference('AdministrationType-4', $siteType);

        $siteType = new AdministrationType();
        $siteType->setLibelle('CSV manuel');
        $manager->persist($siteType);
        $this->addReference('AdministrationType-5', $siteType);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}
