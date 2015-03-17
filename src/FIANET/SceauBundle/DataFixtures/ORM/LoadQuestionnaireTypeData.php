<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\QuestionnaireType;

class LoadQuestionnaireTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /** TODO : 1) les libellés devront être exportés dans le dossier translations
         *         2) Compléter le paramètrage
         */
        $questionnaireType = new QuestionnaireType();
        $questionnaireType->setLibelle('Questionnaire après livraison produit');
        $questionnaireType->setDelaiReception($this->getReference('DelaiReception-3'));
        $questionnaireType->setDelaiEnvoi($this->getReference('DelaiEnvoi-7'));
        $questionnaireType->setNbJoursPourRepondre(90);
        $questionnaireType->setParametrage(array('inscription' => true, 'tombola' => true));
        $manager->persist($questionnaireType);
        $this->addReference('QuestionnaireType-2', $questionnaireType);

        $questionnaireType = new QuestionnaireType();
        $questionnaireType->setLibelle('Questionnaire après achat produit');

        $questionnaireType->setDelaiReception($this->getReference('DelaiReception-0'));
        $questionnaireType->setDelaiEnvoi($this->getReference('DelaiEnvoi-0'));
        $questionnaireType->setNbJoursPourRepondre(30);
        $questionnaireType->setQuestionnaireTypeSuivant($this->getReference('QuestionnaireType-2'));
        $questionnaireType->setParametrage(array('inscription' => true, 'tombola' => true));
        $manager->persist($questionnaireType);
        $this->addReference('QuestionnaireType-1', $questionnaireType);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }
}
