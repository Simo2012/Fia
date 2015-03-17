<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\SiteType;

class LoadSiteTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $siteType = new SiteType();
        $siteType->setLibelle('Site e-commerce');
        $manager->persist($siteType);
        $this->addReference('SiteType-1', $siteType);

        $siteType = new SiteType();
        $siteType->setLibelle('Livreur');
        $manager->persist($siteType);
        $this->addReference('SiteType-2', $siteType);

        $siteType = new SiteType();
        $siteType->setLibelle('Assurance');
        $manager->persist($siteType);
        $this->addReference('SiteType-3', $siteType);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
