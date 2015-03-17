<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\QuestionnairePersonnalisation;
use FIANET\SceauBundle\Entity\Site;


class LoadSiteData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $questionnairePersonnalisation = new QuestionnairePersonnalisation();
        $questionnairePersonnalisation->setQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $questionnairePersonnalisation->setDateDebut(new DateTime());
        $questionnairePersonnalisation->setPrincipal(true);

        $manager->persist($questionnairePersonnalisation);

        $site = new Site();
        $site->setNom('Cdiscount');
        $site->setUrl('http://www.cdiscount.com');
        $site->setPackage($this->getReference('Package-1'));
        $site->setSiteType($this->getReference('SiteType-1'));
        $site->setAdministrationType($this->getReference('AdministrationType-1'));
        $site->setSociete($this->getReference('Societe-1'));
        $site->addQuestionnairePersonnalisation($questionnairePersonnalisation);

        $manager->persist($site);
        $this->addReference('Site-1', $site);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 40;
    }
}
