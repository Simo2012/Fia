<?php
namespace SceauBundle\Service;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use SceauBundle\Entity\Commande;
use SceauBundle\Entity\CommandeCSVParametrage;
use SceauBundle\Entity\Flux;
use SceauBundle\Entity\Questionnaire;
use SceauBundle\Entity\QuestionnairePersonnalisation;
use SceauBundle\Entity\QuestionnaireType;
use SceauBundle\Entity\Site;
use SceauBundle\Entity\SousSite;
use SimpleXMLElement;

/**
 * Service qui permet de générer les questionnaires en fonction de leurs spécificités.
 */
class GestionQuestionnaire
{
    private $em;
    private $codeLangueParDefaut;
    private $delaiEnvoiDateUtilisation;

    public function __construct(EntityManager $em, $codeLangueParDefaut, $delaiEnvoiDateUtilisation)
    {
        $this->em = $em;
        $this->codeLangueParDefaut = $codeLangueParDefaut;
        $this->delaiEnvoiDateUtilisation = $delaiEnvoiDateUtilisation;
    }

    /**
     * Retourne un objet DateInterval en fonction du délai d'envoi passé en argument.
     *
     * @param integer $nbJours Nombre de jours du délai d'envoi
     *
     * @return DateInterval Instance de DateInterval
     */
    private function intervalleDelaiEnvoi($nbJours)
    {
        return new DateInterval('P' . $nbJours . 'D');
    }

    /**
     * Retourne la date d'envoi du questionnaire selon les critères suivants :
     * 1) S'il y a une date d'utilisation, on envoie le questionnaire X jours après cette date (paramètre yml).
     * 2) Si le site a personnalisé le délai d'envoi : on ajoute ce délai à la date du jour.
     * 3) Dans tous les autres cas : on ajoute le délai par défaut en fonction du type de questionnaire.
     *
     * @param QuestionnairePersonnalisation $questionnairePerso Instance de QuestionnairePersonnalisation
     * @param Commande $commande Instance de Commande
     *
     * @return DateTime Retourne la date d'envoi du questionnaire
     */
    private function fixerDateEnvoi(QuestionnairePersonnalisation $questionnairePerso, Commande $commande)
    {
        $dateEnvoi = new DateTime();

        if ($commande->getDateUtilisation()) {
            $dateEnvoi->add(new DateInterval($this->delaiEnvoiDateUtilisation));

        } elseif ($questionnairePerso->getDelaiEnvoi()) {
            $dateEnvoi->add($this->intervalleDelaiEnvoi($questionnairePerso->getDelaiEnvoi()->getNbJours()));

        } else {
            $dateEnvoi->add(
                $this->intervalleDelaiEnvoi($questionnairePerso->getQuestionnaireType()->getDelaiEnvoi()->getNbJours())
            );
        }

        return $dateEnvoi;
    }

    /**
     * Crée une instance de Commande à partir du flux XML envoyé par le marchand.
     *
     * @param Flux $flux Instance de Flux
     * @param string $xml XML de la commande
     *
     * @return Commande Instance de Commande
     */
    private function creerCommandeViaFlux(Flux $flux, $xml)
    {
        $commande = new Commande();
        $commande->setEmail($xml->utilisateur->email->__toString());
        if ($xml->utilisateur->prenom->__toString() != '') {
            $commande->setPrenom($xml->utilisateur->prenom->__toString());
        }
        $commande->setNom($xml->utilisateur->nom->__toString());
        $commande->setDate(new DateTime($xml->infocommande->ip['timestamp']));
        $commande->setReference($xml->infocommande->refid->__toString());
        $commande->setMontant((float)$xml->infocommande->montant->__toString());

        // TODO : Vérifie que le type de questionnaire autorise à utiliser cette balise
        if ($xml->infocommande->dateutilisation) {
            $commande->setDateUtilisation(new DateTime($xml->infocommande->dateutilisation));
        }

        $langue = null;
        if (!$xml->infocommande->langue) {
            $langue = $this->em->getRepository('SceauBundle:Langue')
                ->langueViaCode($this->codeLangueParDefaut);
        } else {
            $langue = $this->em->getRepository('SceauBundle:Langue')
                ->langueViaCode($xml->infocommande->langue->__toString());
            if (!$langue) {
                $langue = $this->em->getRepository('SceauBundle:Langue')
                    ->langueViaCode($this->codeLangueParDefaut);
            }
        }
        $commande->setLangue($langue);
        $commande->setSite($flux->getSite());
        $commande->setFlux($flux);

        return $commande;
    }

    /**
     * Crée une instance de Commande à partir d'une ligne d'un fichier CSV envoyé par le marchand.
     * Seuls le prénom et le montant peuvent être null.
     *
     * @param Site $site Instance de Site
     * @param CommandeCSVParametrage $commandeCSVParametrage Instance de CommandeCSVParametrage
     * @param array $ligne Tableau contenant l'ensemble des colonnes de la ligne
     * @param SousSite|null $sousSite Instance de SousSite. Peut être null.
     *
     * @return Commande Instance de Commande
     */
    private function creerCommandeViaCSV(
        Site $site,
        CommandeCSVParametrage $commandeCSVParametrage,
        $ligne,
        SousSite $sousSite = null
    ) {
        $correspondances = $commandeCSVParametrage->getCorrespondances();

        $commande = new Commande();
        $commande->setEmail($ligne[$correspondances['email']]);
        if ($correspondances['prenom']) {
            $commande->setPrenom($ligne[$correspondances['prenom']]);
        }
        $commande->setNom($ligne[$correspondances['nom']]);

        if (strpos($ligne[$correspondances['date']], '-') !== false) {
            $commande->setDate(new DateTime($ligne[$correspondances['date']]));
        } else {
            /* Date francophone */
            $date = DateTime::createFromFormat('d/m/Y', $ligne[$correspondances['date']]);
            $date->setTime(0, 0, 0);
            $commande->setDate($date);
        }

        $commande->setReference($ligne[$correspondances['reference']]);
        if ($correspondances['montant']) {
            $commande->setMontant($ligne[$correspondances['montant']]);
        }

        $commande->setLangue(
            $this->em->getRepository('SceauBundle:Langue')->langueViaCode($this->codeLangueParDefaut)
        );

        $commande->setSite($site);
        if ($sousSite) {
            $commande->setSousSite($sousSite);
        }

        $listeColonnes = [];
        foreach ($commandeCSVParametrage->getCommandeCSVColonnes() as $commandeCSVColonne) {
            $listeColonnes[] = $commandeCSVColonne->getLibelle();
        }

        $json = [];
        $nbColonnes = count($ligne);
        for ($i = 0; $i < $nbColonnes; $i++) {
            $json[$listeColonnes[$i]] = $ligne[$i];
        }
        $commande->setDonnees($json);

        return $commande;
    }

    /**
     * Insére un questionnaire en base de données.
     *
     * @param Site $site Instance de Site
     * @param QuestionnairePersonnalisation $questionnairePerso Instance de QuestionnairePersonnalisation
     * @param Commande|null $commande Instance de Commande. Peut être null si mode d'administration "lien dans email"
     * @param SousSite|null $sousSite Instance de SousSite. Peut être null.
     */
    private function creerQuestionnaire(
        Site $site,
        QuestionnairePersonnalisation $questionnairePerso,
        Commande $commande = null,
        SousSite $sousSite = null
    ) {
        $questionnaire = new Questionnaire();
        $questionnaire->setCommande($commande);
        if ($sousSite) {
            $questionnaire->setSousSite($sousSite);
        }
        $questionnaire->setSite($site);
        $questionnaire->setLangue($commande->getLangue());
        $questionnaire->setQuestionnaireType($questionnairePerso->getQuestionnaireType());
        $questionnaire->setEmail($commande->getEmail());
        $questionnaire->setDateInsertion(new DateTime());
        $questionnaire->setDatePrevEnvoi(
            $this->fixerDateEnvoi($questionnairePerso, $commande)
        );
        $questionnaire->setActif(true);

        $this->em->persist($questionnaire);
        $this->em->flush($questionnaire);
    }

    /**
     * Retourne l'instance de QuestionnairePersonnalisation dont le QuestionaireType a pour identifiant $id.
     * Si aucun identifiant n'est donné, alors retourne le QuestionnairePersonnalisation "principal".
     *
     * @param QuestionnairePersonnalisation[] $questionnairePersos Tableau d'instance de QuestionnairePersonnalisation
     * @param int|null $id Identifiant. Doit valoir null si on veut récupérer le type de questionnaire principal.
     *
     * @return QuestionnairePersonnalisation
     */
    private function recupQuestPersoParQuestTypeId($questionnairePersos, $id = null)
    {
        $questionnairePerso = null;

        $i = 0;
        while (!$questionnairePerso) {
            if (($id && $questionnairePersos[$i]->getQuestionnaireType()->getId() == $id)
                || (!$id && $questionnairePersos[$i]->getPrincipal() == true)
            ) {
                $questionnairePerso = $questionnairePersos[$i];
            }
            $i++;
        }

        return $questionnairePerso;
    }

    /**
     * Génère un questionnaire à partir d'un flux XML. Attention : le flux passé en argument doit avoir été validé.
     *
     * @param Flux $flux Instance de flux
     */
    public function genererQuestionnaireViaFlux(Flux $flux)
    {
        $xml = new SimpleXMLElement($flux->getXml());
        /** @var QuestionnairePersonnalisation[] $questionnairePersos */
        $questionnairePersos = $flux->getSite()->getQuestionnairePersonnalisations();

        if (!$xml->questionnaire) {
            /* Récupération du type de questionnaire principal */
            $questionnairePerso = $this->recupQuestPersoParQuestTypeId($questionnairePersos);

        } else {
            /* Récupération du type de questionnaire indiqué */
            $questionnairePerso = $this->recupQuestPersoParQuestTypeId($questionnairePersos, $xml->questionnaire);
        }

        $commande = $this->creerCommandeViaFlux($flux, $xml);

        $this->creerQuestionnaire($flux->getSite(), $questionnairePerso, $commande);
    }

    /**
     * Génère un questionnaire à partir d'une ligne d'un fichier CSV.
     *
     * @param Site $site Instance de Site
     * @param QuestionnairePersonnalisation $questionnairePerso Instance de QuestionnairePersonnalisation
     * @param array $ligne Tableau comportant les différentes données de la ligne
     * @param SousSite|null $sousSite Instance de SousSite. Peut être null.
     */
    public function genererQuestionnaireViaCSV(
        Site $site,
        QuestionnairePersonnalisation $questionnairePerso,
        $ligne,
        SousSite $sousSite = null
    ) {
        $commande = $this->creerCommandeViaCSV(
            $site,
            $questionnairePerso->getCommandeCSVParametrage(),
            $ligne,
            $sousSite
        );

        $this->creerQuestionnaire($site, $questionnairePerso, $commande, $sousSite);
    }
}
