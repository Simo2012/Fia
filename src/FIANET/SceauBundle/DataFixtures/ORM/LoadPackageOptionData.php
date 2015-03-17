<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\PackageOption;

class LoadPackageOptionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-2'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-3'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-4'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-5'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-10'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-6'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-1'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-11'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-12'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-8'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-7'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-9'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-3'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-14'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-13'));
        $packageOption->setPackage($this->getReference('Package-1'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-2'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-3'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-4'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-5'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-10'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-6'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-1'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-11'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-12'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-8'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-7'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-9'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-14'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-13'));
        $packageOption->setPackage($this->getReference('Package-2'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-2'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-3'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-4'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-5'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-10'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-6'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-1'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-11'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-12'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-8'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-7'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-9'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-14'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-13'));
        $packageOption->setPackage($this->getReference('Package-3'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-2'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-3'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-4'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-5'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-10'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-6'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-1'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-11'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-12'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-8'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-7'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-9'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-3'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-14'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-13'));
        $packageOption->setPackage($this->getReference('Package-4'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-2'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-3'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-4'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-5'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-10'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-6'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-1'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-11'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-12'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-1'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-8'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-7'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-9'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-3'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-14'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);

        $packageOption = new PackageOption();
        $packageOption->setOption($this->getReference('Option-13'));
        $packageOption->setPackage($this->getReference('Package-5'));
        $packageOption->setOptionType($this->getReference('OptionType-2'));
        $manager->persist($packageOption);
        
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 60;
    }
}
