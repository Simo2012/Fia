<?php

namespace SceauBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Driver\Connection;
use SceauBundle\Entity\QuestionnairePersonnalisation;
use SceauBundle\Entity\Site;
use SceauBundle\Entity\SousSite;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportCSV
{
    private $em;
    private $conn;
    private $validator;
    private $logger;
    private $gestQuest;
    private $site;
    private $questPerso;
    private $pathDossierCsv;
    private $pathCompletCsvEnAttente;
    private $pathCompletCsvTraites;
    private $nbFichiersTraites;
    private $debug;

    public function __construct(
        ObjectManager $em,
        Connection $conn,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        GestionQuestionnaire $gestQuest,
        $pathDossierCsv
    ) {
        $this->em = $em;
        $this->conn = $conn;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->gestQuest = $gestQuest;
        $this->pathDossierCsv = $pathDossierCsv;
    }

    /**
     * Initialise le service avec le site et le type de questionnaire concerné, les différents chemins des
     * dossiers contenants les fichiers CSV, etc.
     *
     * @param Site $site Instance de Site
     * @param QuestionnairePersonnalisation $questPerso Instance de QuestionnairePersonnalisation
     * @param bool true si l'import doit être simulé sinon false
     */
    private function init(Site $site, QuestionnairePersonnalisation $questPerso, $debug)
    {
        $this->site = $site;
        $this->questPerso = $questPerso;
        $this->pathCompletCsvEnAttente = $this->pathDossierCsv . '/' .
             $questPerso->getCommandeCSVParametrage()->getDossierStockage();
        $this->pathCompletCsvTraites = $this->pathCompletCsvEnAttente . '/traites';
        $this->nbFichiersTraites = 0;
        $this->debug = $debug;
    }

    /**
     * Valide une ligne d'un fichier CSV.
     * Vérifie le nombre de colonne, la présence obligatoire ou non des données et le format des données.
     *
     * @param array $ligne Tableau contenant l'ensemble des colonnes de la ligne
     * @param int $numLigne Numéro de la ligne
     *
     * @return bool true si la ligne est valide sinon false
     */
    private function validerLigne($ligne, $numLigne)
    {
        $ligneValide = true;
        $nbColonnes = count($ligne);
        $commandeCSVColonnes =  $this->questPerso->getCommandeCSVParametrage()->getCommandeCSVColonnes();

        if ($nbColonnes == count($commandeCSVColonnes)) {
            for ($numColonne = 0; ($ligneValide && ($numColonne < $nbColonnes)); $numColonne++) {
                $colonne = $ligne[$numColonne];
                $parametrage = $commandeCSVColonnes[$numColonne]->getParametrage();

                if (!$parametrage["obligatoire"] || $colonne != '') {
                    switch ($parametrage["format"]) {
                        case 'string':
                            $listeViolation = [];
                            break;
                        case 'email':
                            $listeViolation = $this->validator->validate($colonne, new Email());
                            break;
                        case 'date':
                            $listeViolation = $this->validator->validate($colonne, new Date());
                            break;
                        case 'datetime':
                            $listeViolation = $this->validator->validate($colonne, new DateTime());
                            break;
                        case 'time':
                            $listeViolation = $this->validator->validate($colonne, new Time());
                            break;
                        case 'regex':
                            $listeViolation = $this->validator->validate(
                                $colonne,
                                new Regex(array('pattern' => $parametrage["pattern"]))
                            );
                            break;
                        case 'enum':
                            $listeViolation = $this->validator->validate(
                                $colonne,
                                new Choice(['choices' => ['O', 'N']])
                            );
                            break;
                        default:
                            $listeViolation = [];
                            break;
                    }

                    if (count($listeViolation) > 0) {
                        $ligneValide = false;

                        $this->logger->warning(
                            sprintf(
                                'Ligne n°%d non valide : la donnée "%s" n\'a pas le bon format (format attendu : %s)',
                                $numLigne,
                                $colonne,
                                $parametrage["format"]
                            )
                        );
                    }

                } else {
                    $ligneValide = false;

                    $this->logger->warning(
                        sprintf(
                            'Ligne n°%d non valide : la colonne "%s" est obligatoire',
                            $numLigne,
                            $commandeCSVColonnes[$numColonne]->getLibelle()
                        )
                    );
                }
            }

            if ($ligneValide) {
                $this->logger->info(sprintf('Ligne n°%d valide', $numLigne));
            }

        } else {
            $ligneValide = false;
            $this->logger->warning(sprintf('Ligne n°%d non valide : il manque des colonnes', $numLigne));
        }

        return $ligneValide;
    }

    /**
     * Vérifie qu'un questionnaire n'a pas été déjà envoyé à une personne.
     * Les critères d'unicité sont stockés dans le paramètrage CSV.
     * Comme on doit faire des recherches dans du JSON, on passe par du SQL.
     *
     * @param array $ligne Tableau contenant l'ensemble des colonnes de la ligne
     * @param int $numLigne Numéro de la ligne
     *
     * @return bool true si la ligne respecte l'unicité sinon false
     */
    private function verifUnicite($ligne, $numLigne)
    {
        $colonnes = $this->questPerso->getCommandeCSVParametrage()->getCommandeCSVColonnes();

        $sql = 'SELECT 1 FROM Commande WHERE site_id='. $this->site->getId();

        foreach ($this->questPerso->getCommandeCSVParametrage()->getUnicite() as $numColonne => $contrainte) {
            $sql .= ' AND ';

            switch ($contrainte['format']) {
                case 'string':
                    $sql .= sprintf(
                        'donnees @> \'{"%s": "%s"}\'',
                        $colonnes[$numColonne]->getLibelle(),
                        $ligne[$numColonne]
                    );
                    break;
                case 'date':
                    if ($contrainte['comparatif'] != '=') {
                        $sql .= sprintf(
                            'to_date(donnees->>\'%s\', \'YYYY-MM-DD\') %s to_date(donnees->>\'%s\', \'YYYY-MM-DD\') - INTERVAL \'%s\'',
                            $colonnes[$numColonne]->getLibelle(),
                            $contrainte['comparatif'],
                            $colonnes[$numColonne]->getLibelle(),
                            $contrainte['intervalle']
                        );

                    } else {
                        $sql .= sprintf(
                            'donnees @> \'{"%s": "%s"}\'',
                            $colonnes[$numColonne]->getLibelle(),
                            $ligne[$numColonne]
                        );
                    }
                    break;
            }
        }
        $sql .= ' LIMIT 1';

        try {
            $unicite = $this->conn->fetchArray($sql);

            if (empty($unicite)) {
                $this->logger->info(sprintf('Ligne n°%d : unicité OK', $numLigne));

                return true;

            } else {
                $this->logger->notice(sprintf('Ligne n°%d : un questionnaire a déjà été envoyé', $numLigne));

                return false;
            }

        } catch (\Exception $e) {
            $this->logger->critical(sprintf('Erreur lors de la vérification de l\'unicité : %s', $e->getMessage()));

            return false;
        }
    }

    /**
     * Récupère s'il existe déjà le sous site lié à une ligne ou sinon le crée en base.
     *
     * @param array $ligne Tableau contenant l'ensemble des colonnes de la ligne
     *
     * @return SousSite Instance de SousSite
     */
    private function recupSousSite($ligne)
    {
        $sousSiteIdClient = $ligne[$this->questPerso->getCommandeCSVParametrage()
            ->getCorrespondances()['sousSiteId']];
        $sousSiteNom = $ligne[$this->questPerso->getCommandeCSVParametrage()
            ->getCorrespondances()['sousSiteNom']];

        $sousSite = $this->em->getRepository('SceauBundle:SousSite')
            ->findOneBy(['site' => $this->site, 'idClient' => $sousSiteIdClient]);

        if (!$sousSite && $sousSiteNom != '') {
            $sousSite = new SousSite();
            $sousSite->setSite($this->site);
            $sousSite->setIdClient($sousSiteIdClient);
            $sousSite->setNom($sousSiteNom);

            $this->em->persist($sousSite);
            $this->em->flush($sousSite);
        }

        return $sousSite;
    }

    /**
     * Insère le contenu de la ligne en base de données.
     * Les données sont "nettoyées" avant insertion : si elles ne sont pas en UTF-8, une conversion est effectuée.
     * Insère également le "sous site" pour les import CSV qui utilisent cette fonctionnalité, s'il n'existe pas déjà.
     *
     * @param array $ligne Tableau contenant l'ensemble des colonnes de la ligne
     *
     * @return bool true si insertion réussie sinon false
     */
    private function insererLigne($ligne)
    {
        array_walk($ligne, function (&$valeur) {
            if (!mb_detect_encoding($valeur, 'UTF-8', true)) {
                $valeur = utf8_encode($valeur);
            }
        });

        try {
            $sousSite = null;
            if ($this->questPerso->getCommandeCSVParametrage()->getSousSiteActif()) {
                $sousSite = $this->recupSousSite($ligne);
            }

            $this->gestQuest->genererQuestionnaireViaCSV($this->site, $this->questPerso, $ligne, $sousSite);

        } catch (\Exception $e) {
            $this->logger->critical(sprintf('Erreur lors de l\'insertion en base : %s', $e->getMessage()));

            return false;
        }

        return true;
    }

    /**
     * Permet de déplacer un fichier une fois que le traitement a été effectué.
     *
     * @param string $cheminFichier Chemin du fichier CSV à déplacer
     */
    private function deplacerFichier($cheminFichier)
    {
        $cheminNewFichier = $this->pathCompletCsvTraites . '/' .
            date('Ymd-Hi') . '- ' . $this->nbFichiersTraites .'.csv';

        if (rename($cheminFichier, $cheminNewFichier)) {
            $this->logger->info(
                sprintf('Le fichier %s a bien été déplacé vers %s', $cheminFichier, $cheminNewFichier)
            );

        } else {
            $this->logger->error(sprintf('Impossible de déplacer le fichier', $cheminFichier));
        }
    }

    /**
     * Parcourt un fichier CSV.
     *
     * @param string $cheminFichier Chemin du fichier CSV à lire
     */
    private function lireFichier($cheminFichier)
    {
        $this->logger->info(sprintf('Lecture du fichier %s', $cheminFichier));

        $fichier = fopen($cheminFichier, 'r');

        if ($fichier !== false) {
            $this->nbFichiersTraites++;

            $numLigne = 0;
            $nbLigneOK = 0;
            $nbLigneKO = 0;
            $nbLigneInseree = 0;
            while ($ligne = fgetcsv($fichier, 0, $this->questPerso->getCommandeCSVParametrage()->getSeparateur())) {
                $numLigne++;

                foreach ($ligne as &$colonne) {
                    $colonne = rtrim($colonne); // Suppression des espaces ajoutés par certains clients...
                }

                if ($this->validerLigne($ligne, $numLigne)) {
                    $nbLigneOK++;

                    if ($this->verifUnicite($ligne, $numLigne)) {
                        if (!$this->debug && $this->insererLigne($ligne)) {
                            $nbLigneInseree++;
                        }
                    }

                } else {
                    $nbLigneKO++;
                }
            }
            fclose($fichier);

            $this->logger->info(sprintf('Nombre de ligne OK : %d', $nbLigneOK));
            $this->logger->warning(sprintf('Nombre de ligne KO : %d', $nbLigneKO));
            $this->logger->info(sprintf('Nombre de ligne insérée : %d', $nbLigneInseree));

            if (!$this->debug) {
                $this->deplacerFichier($cheminFichier);
            }

        } else {
            $this->logger->error(sprintf('Impossible de lire le fichier %s', $cheminFichier));
        }
    }

    /**
     * Importe en base le contenu de l'ensemble des fichiers CSV d'un site et d'un type de questionnaire.
     *
     * @param Site $site Instance de Site
     * @param QuestionnairePersonnalisation $questPerso Instance de QuestionnairePersonnalisation
     * @param bool true si l'import doit être simulé sinon false
     */
    public function importerFichiers(Site $site, QuestionnairePersonnalisation $questPerso, $debug)
    {
        $this->init($site, $questPerso, $debug);

        $this->logger->info(
            sprintf(
                'DEBUT import des fichiers CSV pour le site n°%d et le paramètrage CSV n°%d',
                $site->getId(),
                $questPerso->getCommandeCSVParametrage()->getId()
            )
        );

        if ($repFichiersCSV = @opendir($this->pathCompletCsvEnAttente)) {
            if (is_dir($this->pathCompletCsvTraites)) {
                while (false !== ($nomfichier = readdir($repFichiersCSV))) {
                    $cheminFichier = $this->pathCompletCsvEnAttente . '/' . $nomfichier;

                    /* On traite tous les fichiers présents sauf les liens relatifs '.' '..' et les sous-répertoires */
                    if (filetype($cheminFichier) != 'dir') {
                        $this->lireFichier($cheminFichier);
                    }
                }

                $this->logger->info(sprintf('Nombre de fichiers traités : %d', $this->nbFichiersTraites));

            } else {
                $this->logger->error(sprintf('Impossible d\'ouvrir le répertoire %s', $this->pathCompletCsvTraites));
            }

            closedir($repFichiersCSV);

        } else {
            $this->logger->error(sprintf('Impossible d\'ouvrir le répertoire %s', $this->pathCompletCsvEnAttente));
        }

        $this->logger->info(
            sprintf(
                'FIN import des fichiers CSV pour le site n°%d et le paramètrage CSV n°%d',
                $site->getId(),
                $questPerso->getCommandeCSVParametrage()->getId()
            )
        );
    }
}
