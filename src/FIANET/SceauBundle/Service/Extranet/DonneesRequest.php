<?php

namespace FIANET\SceauBundle\Service\Extranet;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use FIANET\SceauBundle\Entity\QuestionnaireType;
use FIANET\SceauBundle\Service\Notes;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DonneesRequest
{
    private $em;
    private $notes;

    public function __construct(EntityManager $em, Notes $notes)
    {
        $this->em = $em;
        $this->notes = $notes;
    }

    /**
     * Méthode qui récupère les paramètres de recherche en session ou en cookie pour les pages listing et détail des
     * questionnaire.
     *
     * Pour la page listing on regarde dans un premier temps si on provient de la page détail. Si oui, on prends les
     * données en session (liées à la page détail). Sinon on prend les données stockées dans les cookies s'il existent
     * (liées à la sauvegarde des paramètres de recherche par l'utilisateur).
     *
     * @param Request $request Instance de Request
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param int $page 0 pour charger les données pour la page listing ou 1 pour la page détail
     *
     * @return array Tableau contenant l'ensemble des données de filtrages et tris
     */
    public function recupDonneesRequestQuest(Request $request, QuestionnaireType $questionnaireType, $page)
    {
        if ($page == 1) {
            /* Page détail questionnaire */
            $emplacement = $request->getSession();

        } else {
            /* Page listing questionnaire */
            if ($request->getSession()->get('detail_questionnaires') === 1) {
                /* On provient de la page détail questionnaire */
                $emplacement = $request->getSession();

            } else {
                /* On provient d'autre part, on regarde dans les cookies de sauvegarde */
                $emplacement = $request->cookies;
            }
        }

        $donnees['tri'] = is_numeric($emplacement->get('questionnaires_tri')) ?
            $emplacement->get('questionnaires_tri') : 2;
        $donnees['dateDebut'] = $emplacement->get('questionnaires_dateDebut');
        $donnees['dateFin'] = $emplacement->get('questionnaires_dateFin');
        $donnees['indicateurs'] = ($emplacement->get('questionnaires_indicateurs')) ?
            explode('-', $emplacement->get('questionnaires_indicateurs')) : array();
        $donnees['listeReponsesIndicateurs'] = $this->notes
            ->listeReponsesIndicateursPourQuestionnaireType($questionnaireType, $donnees['indicateurs']);
        $donnees['recherche'] = $emplacement->get('questionnaires_recherche');
        $donnees['litige'] = $emplacement->get('questionnaires_litige') ? true : null;

        if ($questionnaireType->getParametrage()['livraison']) {
            if ($emplacement->get('questionnaires_livraison')) {
                $donnees['livraisonType'] = $this->em->getRepository('FIANETSceauBundle:LivraisonType')
                    ->find($emplacement->get('questionnaires_livraison'));
                $donnees['livraison'] = $donnees['livraisonType']->getId();
            } else {
                $donnees['livraisonType'] = null;
                $donnees['livraison'] = '';
            }
        } else {
            $donnees['livraisonType'] = null;
            $donnees['livraison'] = '';
        }

        return $donnees;
    }

    /**
     * Méthode qui sauvegarde les paramètres de recherche des questionnaires en session pour les pages listing/détail
     * des questionnaires.
     *
     * @param Request $request Instance de Request
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param array $valeurs Tableau contenant l'ensemble des données à stocker en session
     */
    public function sauvegardeDonneesSessionQuest(Request $request, QuestionnaireType $questionnaireType, $valeurs)
    {
        $request->getSession()->set('questionnaires_tri', $valeurs['tri']);
        $request->getSession()->set('questionnaires_dateDebut', $valeurs['dateDebut']);
        $request->getSession()->set('questionnaires_dateFin', $valeurs['dateFin']);
        $request->getSession()->set('questionnaires_indicateurs', implode('-', $valeurs['indicateurs']));
        $request->getSession()->set('questionnaires_recherche', $valeurs['recherche']);
        $request->getSession()->set('questionnaires_litige', $valeurs['litige']);

        if ($questionnaireType->getParametrage()['livraison']) {
            if ($valeurs['livraisonType']) {
                $request->getSession()->set('questionnaires_livraison', $valeurs['livraisonType']->getId());

            } else {
                $request->getSession()->remove('questionnaires_livraison');
            }

        } else {
            $request->getSession()->remove('questionnaires_livraison');
        }
    }

    /**
     * Méthode qui sauvegarde les paramètres de recherche des questionnaires en cookie pour la page listing des
     * questionnaires.
     *
     * @param Response $reponse Instance de Response
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param array $valeurs Tableau contenant l'ensemble des données à stocker
     */
    public function sauvegardeDonneesCookieQuest(Response $reponse, QuestionnaireType $questionnaireType, $valeurs)
    {
        $dateExpiration = new DateTime();
        $dateExpiration->add(new DateInterval('P6M'));

        $reponse->headers->setCookie(new Cookie('questionnaires_dateDebut', $valeurs['dateDebut'], $dateExpiration));
        $reponse->headers->setCookie(new Cookie('questionnaires_dateFin', $valeurs['dateFin'], $dateExpiration));
        $reponse->headers->setCookie(
            new Cookie(
                'questionnaires_indicateurs',
                implode('-', $valeurs['indicateurs']),
                $dateExpiration
            )
        );
        $reponse->headers->setCookie(new Cookie('questionnaires_recherche', $valeurs['recherche'], $dateExpiration));
        $reponse->headers->setCookie(new Cookie('questionnaires_litige', $valeurs['litige'], $dateExpiration));

        if ($questionnaireType->getParametrage()['livraison']) {
            if ($valeurs['livraisonType']) {
                $reponse->headers->setCookie(
                    new Cookie(
                        'questionnaires_livraison',
                        $valeurs['livraisonType']->getId(),
                        $dateExpiration
                    )
                );

            } else {
                $reponse->headers->clearCookie('questionnaires_livraison');
            }

        } else {
            $reponse->headers->clearCookie('questionnaires_livraison');
        }

        $reponse->headers->setCookie(new Cookie('questionnaires_tri', $valeurs['tri'], $dateExpiration));
    }
}
