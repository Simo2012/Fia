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
        /*** Site via Certissim ***/
        /* CDiscount */
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
        $site->setAdministrationType($this->getReference('AdministrationType-2'));
        $site->setSociete($this->getReference('Societe-1'));
        $site->addQuestionnairePersonnalisation($questionnairePersonnalisation);

        $manager->persist($site);
        $this->addReference('Site-1', $site);

        /* Site mirroir de Cdiscount */
        $questionnairePersonnalisation = new QuestionnairePersonnalisation();
        $questionnairePersonnalisation->setQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $questionnairePersonnalisation->setDateDebut(new DateTime());
        $questionnairePersonnalisation->setPrincipal(true);

        $manager->persist($questionnairePersonnalisation);

        $site = new Site();
        $site->setNom('GO SPORT');
        $site->setUrl('http://www.go-sport.com');
        $site->setPackage($this->getReference('Package-1'));
        $site->setSiteType($this->getReference('SiteType-1'));
        $site->setAdministrationType($this->getReference('AdministrationType-2'));
        $site->setSociete($this->getReference('Societe-1'));
        $site->addQuestionnairePersonnalisation($questionnairePersonnalisation);
        $site->setSitePrincipal($this->getReference('Site-1'));

        $manager->persist($site);
        $this->addReference('Site-2', $site);

        /*** Site via flux XML***/
        $questionnairePersonnalisation = new QuestionnairePersonnalisation();
        $questionnairePersonnalisation->setQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $questionnairePersonnalisation->setDateDebut(new DateTime());
        $questionnairePersonnalisation->setPrincipal(true);
        $manager->persist($questionnairePersonnalisation);

        $questionnairePersonnalisation2 = new QuestionnairePersonnalisation();
        $questionnairePersonnalisation2->setQuestionnaireType($this->getReference('QuestionnaireType-2'));
        $questionnairePersonnalisation2->setDateDebut(new DateTime('2015-01-01'));
        $questionnairePersonnalisation2->setDateFin(new DateTime('2015-02-15'));
        $questionnairePersonnalisation2->setPrincipal(false);
        $manager->persist($questionnairePersonnalisation2);

        $site = new Site();
        $site->setNom('Maty');
        $site->setUrl('http://www.maty.com');
        $site->setClePrivee('_s5dgbmhivakutu39opt');
        $site->setPackage($this->getReference('Package-3'));
        $site->setSiteType($this->getReference('SiteType-1'));
        $site->setAdministrationType($this->getReference('AdministrationType-1'));
        $site->setSociete($this->getReference('Societe-2'));
        $site->addQuestionnairePersonnalisation($questionnairePersonnalisation);
        $site->addQuestionnairePersonnalisation($questionnairePersonnalisation2);

        $manager->persist($site);
        $this->addReference('Site-3', $site);

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
