<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\OptionType;

class LoadOptionType extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $optionType = new OptionType();
        $optionType->setLibelle('Option de base');
        $manager->persist($optionType);
        $this->addReference('OptionType-1', $optionType);
        
        $optionType = new OptionType();
        $optionType->setLibelle('Option souscriptible');
        $manager->persist($optionType);
        $this->addReference('OptionType-2', $optionType);
        
        $optionType = new OptionType();
        $optionType->setLibelle('Option non souscriptible');
        $manager->persist($optionType);
        $this->addReference('OptionType-3', $optionType);
        
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 50;
    }
}
