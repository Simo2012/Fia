<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\Extranet\MenuElement;

class LoadMenuElementData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /*** Accueil ***/
        $menuElement = new MenuElement();
        $menuElement->setNom('accueil');
        $menuElement->setLibelle('accueil');
        $menuElement->setRoute('extranet_accueil');
        $menuElement->setOrdre(1);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $manager->persist($menuElement);

        /*** Statistiques ***/
        $menuElementStatistiques = new MenuElement();
        $menuElementStatistiques->setNom('statistiques');
        $menuElementStatistiques->setLibelle('statistiques');
        $menuElementStatistiques->setRoute('extranet_statistiques');
        $menuElementStatistiques->setOrdre(2);
        $menuElementStatistiques->setActif(true);
        $menuElementStatistiques->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $manager->persist($menuElementStatistiques);

        /* Statistiques sous éléments */
        $menuElement = new MenuElement();
        $menuElement->setNom('statistiques.dashboard');
        $menuElement->setLibelle('statistiques.dashboard');
        $menuElement->setRoute('extranet_statistiques');
        $menuElement->setOrdre(1);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementStatistiques);
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('statistiques.avant_livraison');
        $menuElement->setLibelle('statistiques.avant_livraison');
        $menuElement->setRoute('extranet_statistiques_avant_livraison');
        $menuElement->setOrdre(2);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementStatistiques);
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('statistiques.apres_livraison');
        $menuElement->setLibelle('statistiques.apres_livraison');
        $menuElement->setRoute('extranet_statistiques_apres_livraison');
        $menuElement->setOrdre(3);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementStatistiques);
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('statistiques.vos_acheteurs');
        $menuElement->setLibelle('statistiques.vos_acheteurs');
        $menuElement->setRoute('extranet_statistiques_vos_acheteurs');
        $menuElement->setOrdre(4);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementStatistiques);
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('statistiques.questions_personnalisees');
        $menuElement->setLibelle('statistiques.questions_personnalisees');
        $menuElement->setRoute('extranet_statistiques_questions_personnalisees');
        $menuElement->setOrdre(5);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementStatistiques);
        $menuElement->setOption($this->getReference('Option-6'));
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('statistiques.vos_donnees');
        $menuElement->setLibelle('statistiques.vos_donnees');
        $menuElement->setRoute('extranet_statistiques_vos_donnees');
        $menuElement->setOrdre(6);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementStatistiques);
        $manager->persist($menuElement);

        /*** Questionnaires ***/
        $menuElementQuestionnaires = new MenuElement();
        $menuElementQuestionnaires->setNom('questionnaires');
        $menuElementQuestionnaires->setLibelle('questionnaires');
        $menuElementQuestionnaires->setRoute('extranet_questionnaires');
        $menuElementQuestionnaires->setOrdre(3);
        $menuElementQuestionnaires->setActif(true);
        $menuElementQuestionnaires->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $manager->persist($menuElementQuestionnaires);

        /* Questionnaires sous éléments */
        $menuElement = new MenuElement();
        $menuElement->setNom('questionnaires.dashboard');
        $menuElement->setLibelle('questionnaires.dashboard');
        $menuElement->setRoute('extranet_questionnaires');
        $menuElement->setOrdre(1);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementQuestionnaires);
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('questionnaires.questionnaires');
        $menuElement->setLibelle('questionnaires.questionnaires');
        $menuElement->setRoute('extranet_questionnaires_questionnaires');
        $menuElement->setOrdre(2);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementQuestionnaires);
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('questionnaires.questions_personnalisees');
        $menuElement->setLibelle('questionnaires.questions_personnalisees');
        $menuElement->setRoute('extranet_questionnaires_questions_personnalisees');
        $menuElement->setOrdre(3);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementQuestionnaires);
        $menuElement->setOption($this->getReference('Option-6'));
        $manager->persist($menuElement);

        $menuElement = new MenuElement();
        $menuElement->setNom('questionnaires.relance_questionnaires');
        $menuElement->setLibelle('questionnaires.relance_questionnaires');
        $menuElement->setRoute('extranet_questionnaires_relance_questionnaires');
        $menuElement->setOrdre(4);
        $menuElement->setActif(true);
        $menuElement->addQuestionnaireType($this->getReference('QuestionnaireType-1'));
        $menuElement->setMenuElementParent($menuElementQuestionnaires);
        $menuElement->setOption($this->getReference('Option-11'));
        $manager->persist($menuElement);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 30;
    }
}
