<?php

namespace SceauBundle\Tests\Service;

use SceauBundle\Entity\CommandeCSVColonne;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImportCSVTest extends KernelTestCase
{
    private $container;
    private $importCsv;
    private $reflector;

    /**
     * Initialise le paramétrage des colonnes CSV.
     *
     * @return CommandeCSVColonne[]
     */
    private function initParametrageColonnes()
    {
        $position = 0;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('num_commande')
            ->setParametrage(['format' => 'string', 'obligatoire' => true]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('date_commande')
            ->setParametrage(['format' => 'date', 'obligatoire' => true]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('email')
            ->setParametrage(['format' => 'email', 'obligatoire' => true]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('date_reception')
            ->setParametrage(['format' => 'datetime', 'obligatoire' => true]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('heure_reception')
            ->setParametrage(['format' => 'time', 'obligatoire' => true]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('reinterrogation')
            ->setParametrage(['format' => 'enum', 'liste' => ['O', 'N'], 'obligatoire' => true]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('date_souscription')
            ->setParametrage(
                ['format' => 'regex', 'pattern' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', 'obligatoire' => true]
            );
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('nom')
            ->setParametrage(['format' => 'string', 'obligatoire' => true]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('prenom')
            ->setParametrage(['format' => 'string', 'obligatoire' => false]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('marchand_id')
            ->setParametrage(['format' => 'string', 'obligatoire' => false]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;
        $commandeCSVColonne = new CommandeCSVColonne();
        $commandeCSVColonne->setPosition($position)->setLibelle('marchand_libelle')
            ->setParametrage(['format' => 'string', 'obligatoire' => false]);
        $commandeCSVColonnes[$position++] = $commandeCSVColonne;

        return $commandeCSVColonnes;
    }

    public function __construct()
    {
        static::bootKernel();

        $this->container = static::$kernel->getContainer();
        $this->importCsv = $this->container->get('sceau.import_csv');

        $site = $this->getMock('SceauBundle\Entity\Site');
        $questionnairePerso = $this->getMock('SceauBundle\Entity\QuestionnairePersonnalisation');
        $commandeCSVParametrage = $this->getMock('SceauBundle\Entity\CommandeCSVParametrage');
        $commandeCSVParametrage->expects($this->once())
            ->method('getDossierStockage')
            ->will($this->returnValue('test-csv'));

        $commandeCSVParametrage->expects($this->once())
            ->method('getCommandeCSVColonnes')
            ->will($this->returnValue($this->initParametrageColonnes()));

        $questionnairePerso->expects($this->any())
            ->method('getCommandeCSVParametrage')
            ->will($this->returnValue($commandeCSVParametrage));

        $this->reflector = new ReflectionClass($this->importCsv);
        $init = $this->reflector->getMethod('init');
        $init->setAccessible(true);
        $init->invokeArgs($this->importCsv, [$site, $questionnairePerso, true]);
    }

    /**
     * Permet d'appeler la méthode privée validerLigne() de la classe ImportCSV.
     *
     * @param array $colonnes Tableau contenant les valeurs de chaque colonne de la ligne
     *
     * @return bool true si la ligne est valide sinon false
     */
    private function validerLigne($colonnes)
    {
        $validerLigne = $this->reflector->getMethod('validerLigne');
        $validerLigne->setAccessible(true);

        return $validerLigne->invokeArgs($this->importCsv, $colonnes);
    }

    /**
     * Ligne parfaite => valide
     */
    public function testLigneCompleteValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(true, $result);
    }

    /**
     * Il manque une (des) donnée(s) non obligatoire(s) => valide
     */
    public function testLigneIncompleteValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte', 8 => '',
                9 => '', 10 => ''], 1]
        );

        $this->assertEquals(true, $result);
    }

    /**
     * Il manque une donnée obligatoire => non valide
     */
    public function testDonneeObligatoireManquante()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }

    /**
     * Le nombre de colonne ne correspond pas au paramétrage => non valide
     */
    public function testNbColonneNonValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte',
                8 => '4241', 9 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }

    /**
     * Format email incorrect => non valide
     */
    public function testEmailNonValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }

    /**
     * Format date incorrect => non valide
     */
    public function testDateNonValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '201507-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }


    /**
     * Format datetime incorrect => non valide
     */
    public function testDatetimeNonValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 45:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }

    /**
     * Format time incorrect => non valide
     */
    public function testTimeNonValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13::12', 5 => 'N', 6 => '30/07/2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }

    /**
     * Format regex incorrect => non valide
     */
    public function testRegexNonValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'N', 6 => '30-07-2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }

    /**
     * Format enum incorrect => non valide
     */
    public function testEnumNonValide()
    {
        $result = $this->validerLigne(
            [[0 => '432694288', 1 => '2015-07-30', 2 => 'sdsite3@gmail.com', 3 => '2015-08-07 13:45:12',
                4 => '13:45:12', 5 => 'Oui', 6 => '30/07/2015', 7 => 'Delaporte', 8 => 'Sébastien',
                9 => '4241', 10 => 'Foteam'], 1]
        );

        $this->assertEquals(false, $result);
    }
}
