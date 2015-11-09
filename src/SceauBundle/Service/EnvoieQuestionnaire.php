<?php

namespace SceauBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\EnvoiEmail;
use SceauBundle\Entity\Questionnaire;
use SceauBundle\Entity\QuestionnairePersonnalisation;
use SceauBundle\Entity\QuestionnaireType;
use SceauBundle\Entity\Repository\QuestionnaireRepository;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Service qui se charge d'envoyer les e-mails des différents types de questionnaires.
 */
class EnvoieQuestionnaire
{
    private $em;
    private $templating;
    private $trans;

    public function __construct(ObjectManager $em, EngineInterface $templating, TranslatorInterface $trans)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->trans = $trans;
    }

    /**
     * Ajoute un nombre aléatoire à une adresse mail. Attention la chaine de caractère fournie doit comporter une
     * chaine de format acceptant un entier.
     *
     * @param string $expediteur Adresse mail à modifier
     *
     * @return string L'adresse mail modifiée
     */
    private function randomExpediteur($expediteur)
    {
        return sprintf($expediteur, rand()%1000000);
    }

    /**
     * Permet de récupérer l'expéditeur de l'e-mail.
     * 1) On regarde d'abord si une personnalisation existe pour le site.
     * 2) Sinon on prend l'expéditeur global du type de questionnaire.
     * L'expéditeur peut être formé d'une partie aléatoire.
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     *
     * @return string L'expéditeur
     */
    private function getSendFrom(Questionnaire $questionnaire)
    {
        /** @var QuestionnairePersonnalisation $questPerso */
        $questPerso = $questionnaire->getSite()->getQuestionnairePersonnalisations()[0];

        if ($questPerso->getExpediteur() !== null) {
            if ($questPerso->getExpediteurRand() === true) {
                return $this->randomExpediteur($questPerso->getExpediteur());

            } else {
                return $questPerso->getExpediteur();
            }
        } else {
            $param = $questionnaire->getQuestionnaireType()->getParametrage()['email'];

            if ($param['expediteurRand']) {
                return $this->randomExpediteur($param['expediteur']);

            } else {
                return $param['expediteur'];
            }
        }
    }

    /**
     * Crée l'e-mail d'un questionnaire.
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     */
    private function creerEmail(Questionnaire $questionnaire)
    {
        $param = $questionnaire->getQuestionnaireType()->getParametrage()['email'];

        if ($questionnaire->getCommande()) {
            $reference = $questionnaire->getCommande()->getReference();
            $date = $questionnaire->getCommande()->getDate();
            $nom = $questionnaire->getMembre()->getNom();
            $prenom = $questionnaire->getMembre()->getPrenom();

        } else {
            $reference = null;

            $questionnaireLie = $questionnaire->getQuestionnaireLie();
            if ($questionnaireLie) {
                $date = $questionnaireLie->getDateInsertion();
            } else {
                $date = $questionnaire->getDateInsertion();
            }

            if ($questionnaire->getMembre()) {
                $nom = $questionnaire->getMembre()->getNom();
                $prenom = $questionnaire->getMembre()->getPrenom();

            } else {
                $nom = null;
                $prenom = null;
            }
        }
        $locale = $questionnaire->getLangue()->getCode();
        $siteNom = $questionnaire->getSite()->getNom();
        $subject = $this->trans->trans($param['objet'], ['%site_nom%' => $siteNom], 'emails_questionnaires', $locale);
        $livraisonPrevue = $questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant() ? true : false;

        $content = $this->templating->render(
            '@Sceau/Emails/Questionnaires/'. $param['template'] .'.html.twig',
            ['html' => true, 'variables' =>
                [
                    'site' => [
                        'nom' => $siteNom,
                        'url' => $questionnaire->getSite()->getUrl(),
                        'logo' => $questionnaire->getSite()->getLogo()
                    ],
                    'commande' => [
                        'reference' => $reference,
                        'date' => $date
                    ],
                    'email' => $questionnaire->getEmail(),
                    'nom' => $nom,
                    'prenom' => $prenom
                ],
                'locale' => $locale,
                'livraisonPrevue' => $livraisonPrevue,
                'tombola' => $questionnaire->getQuestionnaireType()->getParametrage()['tombola']
            ]
        );

        // TODO : service de création d'un email ?
        $email = new EnvoiEmail();
        $email->setContent($content);
        $email->setSubject($subject);
        $email->setSendFrom($this->getSendFrom($questionnaire));
        $email->setSendTo($questionnaire->getEmail());
        $email->setStatus(EnvoiEmail::NOT_SENT);

        $this->em->persist($email);
    }

    /**
     * Envoie les e-mails contenant un lien vers un questionnaire. Si une instance de QuestionnaireType est passé en
     * argument alors la méthode n'envoie que des questionnaires de ce type (par défaut vaut null).
     *
     * @param int $nbQuestionnaire Nombre maximum de questionnaire a envoyer
     * @param QuestionnaireType|null $questionnaireType Instance de QuestionnaireType ou null
     */
    public function envoyer($nbQuestionnaire, QuestionnaireType $questionnaireType = null)
    {
        /** @var QuestionnaireRepository $questionnaireRepo */
         $questionnaireRepo = $this->em->getRepository('SceauBundle:Questionnaire');
         $questionnaires = $questionnaireRepo->aEnvoyer($nbQuestionnaire, $questionnaireType);

        /** @var Questionnaire $questionnaire */
        foreach ($questionnaires as $questionnaire) {
            $this->creerEmail($questionnaire);

            $questionnaire->setDateEnvoi(new \DateTime());
        }

        $this->em->flush();
    }
}
