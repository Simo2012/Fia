<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\Option;

class LoadOptionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $option = new Option();
        $option->setLibelle("Visualisations des données");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-1', $option);
        
        $option = new Option();
        $option->setLibelle("Extractions des données personnalisées");
        $option->setDescriptif("Téléchargement l'ensemble des données issue de l'extranet");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-2', $option);

        $option = new Option();
        $option->setLibelle("Bons plans");
        $option->setDescriptif("Encart publicitaire mettant en avant votre site ou un produit vendu sur votre site avec un code de réduction de type FIA-NET dans l'espace membre du site");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-3', $option);
        
        $option = new Option();
        $option->setLibelle("Newsletter");
        $option->setDescriptif("Encart publicitaire mettant en avant votre site ou un produit vendu sur votre site avec un code de réduction de type FIA-NET dans une newsletter mensuelle");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-4', $option);
        
        $option = new Option();
        $option->setLibelle("Réseaux sociaux");
        $option->setDescriptif("Page permettant de publier vos commentaires clients sur les réseaux sociaux (Facebook, Twitter,...) pendant une durée de 1 an");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-5', $option);
        
        $option = new Option();
        $option->setLibelle("Personnalisation des questionnaires");
        $option->setDescriptif("Possibilité d'ajouter des questions personnalisées au questionnaire après achat ou au questionnaire après livraison");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-6', $option);

        $option = new Option();
        $option->setLibelle("Personnalisation des e-mails");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-7', $option);
        
        $option = new Option();
        $option->setLibelle("Questionnaires en langues étrangères");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-8', $option);
        
        $option = new Option();
        $option->setLibelle("Publication des commentaires (version basique)");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-9', $option);

        $option = new Option();
        $option->setLibelle("Publication des commentaires (version expert)");
        $option->setDescriptif("Page permettant de personnaliser pendant une durée de 1 an votre flux XML contenant les commentaires de vos clients");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-10', $option);

        $option = new Option();
        $option->setLibelle("Relance questionnaire");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-11', $option);
        
        $option = new Option();
        $option->setLibelle("Google");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-12', $option);
        
        $option = new Option();
        $option->setLibelle("Avis produits");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-13', $option);
        
        $option = new Option();
        $option->setLibelle("Benchmark");
        $option->setDescriptif("");
        $option->setActif(true);
        $manager->persist($option);
        $this->addReference('Option-14', $option);
        
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 20;
    }
}
