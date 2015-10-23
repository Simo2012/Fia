<?php

namespace SceauBundle\Controller\Extranet;

use Exception;
use SceauBundle\Entity\DroitDeReponse;
use SceauBundle\Entity\Langue;
use SceauBundle\Entity\Question;
use SceauBundle\Entity\QuestionType;
use SceauBundle\Entity\Relance;
use SceauBundle\Exception\Extranet\AccesInterditException;
use SceauBundle\Form\Type\Extranet\QuestionnairesImportType;
use SceauBundle\Form\Type\RelanceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class QuestionnairesController
 * @package SceauBundle\Controller\Extranet
 * @Route("/questionnaires")
 */
class QuestionnairesController extends Controller
{
    /**
     * Affiche la page de listing des questionnaires. Les questionnaires affichés dépendent des filtres et tris demandés
     * par l'utilisateur.
     *
     * @Route("/questionnaires", name="extranet_questionnaires_questionnaires")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response Instance de Response
     */
    public function questionnairesAction(Request $request)
    {
        $nbQuestionnairesMax = $this->container->getParameter('nb_questionnaires_max');
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');

        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

        $form = $this->createForm('sceaubundle_questionnaires_liste', null);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            /* Affichage de la page sans soumission du formulaire : on récupère les éventuels paramètres de
            recherche sauvegardés dans les cookies ou en session si on provient de la page détail. */
            $donnees = $this->get('sceau.extranet.donnees_request')
                ->recupDonneesRequestQuest($request, $questionnaireType, 0);

            $form->get('dateDebut')->setData($donnees['dateDebut']);
            $form->get('dateFin')->setData($donnees['dateFin']);
            $form->get('indicateurs')->setData($donnees['indicateurs']);
            $form->get('recherche')->setData($donnees['recherche']);
            $form->get('litige')->setData($donnees['litige']);

            if ($questionnaireType->getParametrage()['livraison']) {
                $form->get('livraison')->setData($donnees['livraisonType']);
            }

            $retenir = false;

        } else {
            /* Soumission du formulaire */
            $donneesForm = $form->getData();
            $donnees['tri'] = is_numeric($donneesForm['tri']) ? $donneesForm['tri'] : 2;
            $donnees['dateDebut'] = (isset($donneesForm['dateDebut'])) ? $donneesForm['dateDebut'] : '';
            $donnees['dateFin'] = (isset($donneesForm['dateFin'])) ? $donneesForm['dateFin'] : '';
            $donnees['indicateurs'] = (isset($donneesForm['indicateurs'])) ? $donneesForm['indicateurs'] : array();
            $donnees['recherche'] = (isset($donneesForm['recherche'])) ? $donneesForm['recherche'] : '';
            $donnees['litige'] = $donneesForm['litige'] ? true : null;

            $donnees['livraisonType'] = $questionnaireType->getParametrage()['livraison'] ?
                $donneesForm['livraison'] : null;
            $donnees['livraison'] = $donnees['livraisonType'] ? $donnees['livraisonType']->getId() : '';

            $retenir = $donneesForm['retenir'];
        }

        if (!$form->isSubmitted() || ($form->isSubmitted() && $form->isValid())) {
            /* Affichage de la page sans soumission du formulaire ou formulaire soumis et valide */

            $site = $request->getSession()->get('siteSelectionne');

            $donnees['listeReponsesIndicateurs'] = $this->get('sceau.notes')
                ->listeReponsesIndicateursPourQuestionnaireType($questionnaireType, $donnees['indicateurs']);

            $nbTotalQuestionnaires = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')
                ->nbTotalQuestionnaires(
                    $site,
                    $questionnaireType,
                    $donnees['dateDebut'],
                    $donnees['dateFin'],
                    $donnees['recherche'],
                    $donnees['listeReponsesIndicateurs'],
                    $donnees['livraisonType']
                );

            $questionnaires = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')
                ->listeQuestionnaires(
                    $site,
                    $questionnaireType,
                    $donnees['dateDebut'],
                    $donnees['dateFin'],
                    $donnees['recherche'],
                    $donnees['listeReponsesIndicateurs'],
                    $donnees['livraisonType'],
                    0,
                    $nbQuestionnairesMax,
                    $donnees['tri']
                );

            /* Sauvegarde en session des paramètres de recherche pour la page détail d'un questionnaire */
            $this->get('sceau.extranet.donnees_request')->sauvegardeDonneesSessionQuest(
                $request,
                $questionnaireType,
                array(
                    'tri' =>  $donnees['tri'],
                    'dateDebut' => $donnees['dateDebut'],
                    'dateFin' => $donnees['dateFin'],
                    'indicateurs' => $donnees['indicateurs'],
                    'recherche' => $donnees['recherche'],
                    'litige' => $donnees['litige'],
                    'livraisonType' => $donnees['livraisonType']
                )
            );

        } else {
            /* Formulaire soumis et non valide */
            $nbTotalQuestionnaires = 0;
            $questionnaires = array();
        }

        $reponse =  $this->render(
            'SceauBundle:Extranet/Questionnaires:questionnaires.html.twig',
            array(
                'nbTotalQuestionnaires' => $nbTotalQuestionnaires,
                'questionnaires' => $questionnaires,
                'nbQuestionnairesMax' => $nbQuestionnairesMax,
                'form' => $form->createView(),
                'offset' => 0,
                'dateDebut' => $donnees['dateDebut'],
                'dateFin' => $donnees['dateFin'],
                'tri' =>  $donnees['tri'],
                'recherche' => $donnees['recherche'],
                'indicateurs' => implode('-', $donnees['indicateurs']),
                'livraison' => $donnees['livraison'],
                'parametrage' => $questionnaireType->getParametrage(),
                'urlRedirection' => $this->generateUrl('extranet_questionnaires_questionnaires', array(), true)
            )
        );

        /* Sauvegarde des paramètres de recherche */
        if ($retenir) {
            $this->get('sceau.extranet.donnees_request')->sauvegardeDonneesCookieQuest(
                $reponse,
                $questionnaireType,
                array(
                    'tri' =>  $donnees['tri'],
                    'dateDebut' => $donnees['dateDebut'],
                    'dateFin' => $donnees['dateFin'],
                    'indicateurs' => $donnees['indicateurs'],
                    'recherche' => $donnees['recherche'],
                    'litige' => $donnees['litige'],
                    'livraisonType' => $donnees['livraisonType']
                )
            );
        }

        $request->getSession()->set('detail_questionnaires', 0);

        return $reponse;
    }

    /**
     * Action appelable uniquement via AJAX. Elle retourne les questionnaires par paquet de lignes de tableau en
     * fonction des filtres demandés (utilisé pour le scroll infini).
     *
     * @Route("/questionnaires-ajax", name="extranet_questionnaires_questionnaires_ajax",
     *     options={"expose"=true})
     * @Method({"POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response Instance de Response
     *
     * @throws Exception Si l'action n'est pas appelée en AJAX
     */
    public function questionnairesAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
            $indicateurs = ($request->request->get('indicateurs')) ? $request->request->get('indicateurs') : array();

            if ($questionnaireType->getParametrage()['livraison'] && $request->request->get('livraison')) {
                $livraisonType = $this->getDoctrine()->getRepository('SceauBundle:LivraisonType')
                    ->find($request->request->get('livraison'));
            } else {
                $livraisonType = null;
            }

            $listeReponsesIndicateurs = $this->get('sceau.notes')
                ->listeReponsesIndicateursPourQuestionnaireType($questionnaireType, $indicateurs);

            $questionnaires = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')
                ->listeQuestionnaires(
                    $request->getSession()->get('siteSelectionne'),
                    $questionnaireType,
                    $request->request->get('dateDebut'),
                    $request->request->get('dateFin'),
                    $request->request->get('recherche'),
                    $listeReponsesIndicateurs,
                    $livraisonType,
                    $request->request->get('offset', 0),
                    $this->container->getParameter('nb_questionnaires_max'),
                    $request->request->get('tri')
                );

            return $this->render(
                'SceauBundle:Extranet/Questionnaires:questionnaires_lignes.html.twig',
                array(
                    'questionnaires' => $questionnaires,
                    'offset' => $request->request->get('offset', 0),
                    'parametrage' => $questionnaireType->getParametrage()
                )
            );

        } else {
            throw new Exception($this->get('translator')->trans(
                'erreurs_mauvaise_methode_appel',
                array(),
                'erreurs',
                $request->getLocale()
            ));
        }
    }

    /**
     * Affichage la page de création de question personnalisée.
     *
     * @Route("/questions-personnalisees", name="extranet_questionnaires_questions_personnalisees")
     * @Route("/questions-personnalisees/{id}",
     *     name="extranet_questionnaires_questions_personnalisees_question_type", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param QuestionType $questionType
     *
     * @return RedirectResponse|Response Instance de Response ou RedirectResponse
     */
    public function questionPersoAction(Request $request, QuestionType $questionType = null)
    {
        $elementMenu = $this->get('sceau.extranet.menu')
            ->getChild('questionnaires')->getChild('questionnaires.questions_personnalisees');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $site = $em->merge($request->getSession()->get('siteSelectionne'));
        $questionnaireType = $em->merge($request->getSession()->get('questionnaireTypeSelectionne'));
        $questionsEnAttenteDeValidation = $em->getRepository('SceauBundle:Question')
            ->questionsPersosEnAttenteDeValidation($site, $questionnaireType);

        $question = new Question();
        $question->setSite($site);
        $question->addQuestionnaireType($questionnaireType);
        if ($questionType) {
            $question->setQuestionType($questionType);
        }

        $form = $this->createForm('sceaubundle_question_perso', $question);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->get('sceau.questionnaire_structure')->ajouterQuestionPerso($question);

            } catch (Exception $e) {
                $request->getSession()->getFlashBag()->add(
                    'creation_erreur',
                    $translator->trans('probleme_technique', array(), 'erreurs')
                );

                return $this->redirect($this->generateUrl('extranet_questionnaires_questions_personnalisees'));
            }

            $request->getSession()->getFlashBag()->add(
                'creation_succes',
                $translator->trans('message_succes', array(), 'extranet_questionnaires_question_perso')
            );

            return $this->redirect($this->generateUrl('extranet_questionnaires_questions_personnalisees'));
        }

        return $this->render(
            'SceauBundle:Extranet/Questionnaires:question_perso.html.twig',
            array(
                'form' => $form->createView(),
                'questionType' => $questionType,
                'questionsEnAttenteDeValidation' => $questionsEnAttenteDeValidation,
                'urlRedirection' => $this->generateUrl(
                    'extranet_questionnaires_questions_personnalisees_question_type',
                    array('id' => ($questionnaireType) ? $questionnaireType->getId() : null),
                    true
                )
            )
        );
    }

    /**
     * Affiche la page de relance des questionnaires. Si un identifiant de langue est passé en plus dans l'URL, la
     * liste déroulante des langues est initialisée avec cette langue.
     *
     * @Route("/relance-questionnaires", name="extranet_questionnaires_relance_questionnaires")
     * @Route("/relance-questionnaires/{langue_id}", requirements={"langue_id" = "\d+"},
     *     name="extranet_questionnaires_relance_questionnaires_langue")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param integer|null $langue_id Identifiant de la langue
     *
     * @return Response Instance de Response
     */
    public function relanceAction(Request $request, $langue_id = null)
    {
        $menu = $this->get('sceau.extranet.menu');
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        $em = $this->getDoctrine()->getManager();

        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        $site = $request->getSession()->get('siteSelectionne');
        $datePeriode = $this->get('sceau.relance')->calculerPeriode();
        $nbQuestionnairesMax = $this->container->getParameter('nb_relances_max');

        $form = $this->createForm('sceaubundle_select_langue');
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $langue = $form->get('langue')->getData();

        } else {
            if ($langue_id) {
                $langue = $em->getRepository('SceauBundle:Langue')->langueViaId($langue_id);

            } else {
                $langue = $em->getRepository('SceauBundle:Langue')
                    ->langueViaCode($this->container->getParameter('langue_par_defaut'));
            }

            $form->get('langue')->setData($langue);
        }

        $nbTotalQuestionnaires = $em->getRepository('SceauBundle:Questionnaire')
            ->nbTotalQuestionnairesARelancer(
                $site,
                $questionnaireType,
                $datePeriode['dateDebut'],
                $datePeriode['dateFin'],
                $langue->getId()
            );

        $questionnaires = $em->getRepository('SceauBundle:Questionnaire')
            ->listeQuestionnairesARelancerParPaquet(
                $site,
                $questionnaireType,
                $datePeriode['dateDebut'],
                $datePeriode['dateFin'],
                $langue->getId(),
                0,
                $nbQuestionnairesMax
            );

        $relanceValidee = $em->getRepository('SceauBundle:Relance')
            ->relanceValidee($site, $questionnaireType, $langue);
        if ($relanceValidee && $relanceValidee->getAuto()) {
            $auto = true;
        } else {
            $auto = false;
        }

        return $this->render(
            'SceauBundle:Extranet/Questionnaires:relance.html.twig',
            array(
                'form' => $form->createView(),
                'delaiJoursRelance' => $this->container->getParameter('relance_delai_jours'),
                'nbTotalQuestionnaires' => $nbTotalQuestionnaires,
                'nbQuestionnairesMax' => $nbQuestionnairesMax,
                'questionnaires' => $questionnaires,
                'offset' => 0,
                'dateDebut' => $datePeriode['dateDebut'],
                'dateFin' => $datePeriode['dateFin'],
                'langue_id' => $langue->getId(),
                'templateEmail' => $questionnaireType->getParametrage()['templateEmail'],
                'auto' => $auto,
                'site_nom' => $site->getNom(),
                'urlRedirection' => $this->generateUrl(
                    'extranet_questionnaires_relance_questionnaires_langue',
                    array('langue_id' =>  $langue->getId()),
                    true
                )
            )
        );
    }

    /**
     * Action appelable uniquement via AJAX. Elle retourne les questionnaires pouvant être relancés par paquet
     * de lignes de tableau (utilisé pour le scroll infini).
     *
     * @Route("/relance-questionnaires-ajax", name="extranet_questionnaires_relance_ajax",
     *     options={"expose"=true})
     * @Method({"POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response Instance de Response
     *
     * @throws Exception Si l'action n'est pas appelée en AJAX
     */
    public function relanceAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $datePeriode = $this->get('sceau.relance')->calculerPeriode();

            $questionnaires = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')
                ->listeQuestionnairesARelancerParPaquet(
                    $request->getSession()->get('siteSelectionne'),
                    $request->getSession()->get('questionnaireTypeSelectionne'),
                    $datePeriode['dateDebut'],
                    $datePeriode['dateFin'],
                    $request->request->get('langue_id'),
                    $request->request->get('offset', 0),
                    $this->container->getParameter('nb_relances_max')
                );

            return $this->render(
                'SceauBundle:Extranet/Questionnaires:relance_lignes.html.twig',
                array(
                    'questionnaires' => $questionnaires,
                    'offset' => $request->request->get('offset', 0)
                )
            );

        } else {
            throw new Exception($this->get('translator')->trans(
                'erreurs_mauvaise_methode_appel',
                array(),
                'erreurs',
                $request->getLocale()
            ));
        }
    }

    /**
     * Active ou désactive la relance automatique.
     *
     * @Route("/relance-auto", name="extranet_questionnaires_relance_auto",
     *     options={"expose"=true})
     * @Method({"POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response Instance de Response
     *
     * @throws Exception Si l'action n'est pas appelée en AJAX
     */
    public function relanceAutomatisationAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
            $questionnaireType = $this->getDoctrine()
                ->getManager()->merge($request->getSession()->get('questionnaireTypeSelectionne'));
            $gestionRelance = $this->get('sceau.relance');

            $langue = $this->getDoctrine()->getRepository('SceauBundle:Langue')
                ->langueViaId($request->request->get('langue_id'));

            if ($request->request->get('activer') == 'true') {
                return new JsonResponse($gestionRelance->automatiser($site, $questionnaireType, $langue));
            } else {
                return new JsonResponse($gestionRelance->desautomatiser($site, $questionnaireType, $langue));
            }

        } else {
            throw new Exception($this->get('translator')->trans(
                'erreurs_mauvaise_methode_appel',
                array(),
                'erreurs',
                $request->getLocale()
            ));
        }
    }

    /**
     * Renvoie les questionnaires non répondus pour un site, un type de questionnaire et une langue suite à une
     * demande de relance de l'utilisateur.
     *
     * @Route("/renvoyer/{langue_id}", requirements={"langue_id" = "\d+"},
     *     name="extranet_questionnaires_relance_renvoyer")
     * @Method("GET")
     *
     * @param Request $request Instance de Request
     * @param integer $langue_id Identifiant de la langue
     *
     * @return Response Instance de Response
     */
    public function relanceRenvoyerAction(Request $request, $langue_id)
    {
        $translator = $this->get('translator');

        if ($this->get('sceau.relance')->renvoyerQuestionnaires(
            $request->getSession()->get('siteSelectionne'),
            $request->getSession()->get('questionnaireTypeSelectionne'),
            $langue_id
        )) {
            $this->get('session')->getFlashBag()->add(
                'renvoyer_succes',
                $translator->trans('renvoyer_succes', array(), 'extranet_questionnaires_relance')
            );

        } else {
            $this->get('session')->getFlashBag()->add(
                'renvoyer_erreur',
                $translator->trans('renvoyer_erreur', array(), 'extranet_questionnaires_relance')
            );
        }

        return $this->redirect($this->generateUrl(
            'extranet_questionnaires_relance_questionnaires_langue',
            array('langue_id' => $langue_id)
        ));
    }

    /**
     * Affiche la page et traite le formulaire qui permet de personnaliser la relance pour un type de questionnaire.
     *
     * @Route("/personnaliser-relance/{langue_id}", requirements={"langue_id" = "\d+"},
     *     name="extranet_questionnaires_relance_perso")
     * @ParamConverter("langue", class="SceauBundle:Langue", options={"id" = "langue_id"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param Langue $langue Instance de Langue
     *
     * @return Response Instance de Response
     */
    public function relancePersonnaliserEmailAction(Request $request, Langue $langue)
    {
        $menu = $this->get('sceau.extranet.menu');
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        $em = $this->getDoctrine()->getManager();

        $questionnaireType = $em->merge($request->getSession()->get('questionnaireTypeSelectionne'));
        $site = $em->merge($request->getSession()->get('siteSelectionne'));

        $relance = new Relance();
        $relance->setSite($site);
        $relance->setQuestionnaireType($questionnaireType);
        $relance->setLangue($langue);

        $form = $this->createForm(new RelanceType(), $relance);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($this->get('sceau.relance')->creer($site, $questionnaireType, $langue, $form->getData())) {
                $message = 'personnaliser_succes';
            } else {
                $message = 'personnaliser_erreur';
            }

            $this->get('session')->getFlashBag()->add(
                $message,
                $this->get('translator')->trans($message, array(), 'extranet_questionnaires_relance_perso')
            );

            return $this->redirect($this->generateUrl(
                'extranet_questionnaires_relance_questionnaires_langue',
                array('langue_id' => $langue->getId())
            ));
        }

        return $this->render(
            'SceauBundle:Extranet/Questionnaires:relance_perso.html.twig',
            array(
                'templateEmail' => $questionnaireType->getParametrage()['templateEmail'],
                'objetParDefaut' => sprintf(
                    $this->container->getParameter('relance_objet_par_defaut'),
                    $site->getNom()
                ),
                'form' => $form->createView(),
                'site_nom' => $site->getNom(),
                'urlRedirection' => $this->generateUrl('extranet_questionnaires_relance_questionnaires')
            )
        );
    }

    /**
     * Affiche la page de détail d'un questionnaire (avis)
     *
     * @Route("/detail-questionnaire/{questionnaire_id}/{position}",
     *     requirements={"questionnaire_id" = "\d+", "position" = "\d+"},
     *     name="extranet_questionnaires_detail_questionnaire_pagine")
     * @Route("/detail-questionnaire/{questionnaire_id}", requirements={"questionnaire_id" = "\d+"},
     *     name="extranet_questionnaires_detail_questionnaire")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param int $questionnaire_id Identifiant du questionnaire
     * @param int|null $position Position du questionnaire dans le listing filtré et trié
     *
     * @return Response Instance de Response
     *
     * @throws Exception
     */
    public function detailQuestionnaireAction(Request $request, $questionnaire_id, $position = null)
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');

        if (!$this->getDoctrine()->getManager()->getRepository('SceauBundle:Questionnaire')
            ->verifierExistenceEtLiaisonAvecSite($questionnaire_id, $site)) {
            throw new Exception($this->get('translator')->trans('questionnaire_invalide', array(), 'erreurs'));
        }

        $questionnaires = $this->get('sceau.questionnaire_repondu')
            ->recupStructureQuestionnaireAvecReponses($site, $questionnaire_id);

        $donneesRequest = $this->get('sceau.extranet.donnees_request')
            ->recupDonneesRequestQuest($request, $questionnaireType, 1);
        $request->getSession()->set('detail_questionnaires', 1);

        if ($position !== null) {
            $pagination = $this->getDoctrine()->getManager()->getRepository('SceauBundle:Questionnaire')
                ->questionnairesSuivantEtPrecedent(
                    $site,
                    $questionnaireType,
                    $donneesRequest['dateDebut'],
                    $donneesRequest['dateFin'],
                    $donneesRequest['recherche'],
                    $donneesRequest['listeReponsesIndicateurs'],
                    $donneesRequest['livraisonType'],
                    $donneesRequest['tri'],
                    $position
                );

            if (count($pagination) > 1) {
                if ($pagination[0]['id'] == $questionnaire_id) {
                    $boutons = array('precedent' => null, 'suivant' => $pagination[1]);
                } elseif (!isset($pagination[2]['id'])) {
                    $boutons = array('precedent' => $pagination[0], 'suivant' => null);
                } else {
                    $boutons = array('precedent' => $pagination[0], 'suivant' => $pagination[2]);
                }

            } else {
                $boutons = array('precedent' => null, 'suivant' => null);
            }

        } else {
            $boutons = array('precedent' => null, 'suivant' => null);
        }

        return $this->render(
            'SceauBundle:Extranet/Questionnaires:detail_questionnaire.html.twig',
            array(
                'questionnaire1' => $questionnaires['questionnaire1'],
                'questionnaire2' => $questionnaires['questionnaire2'],
                'boutons' => $boutons,
                'position' => $position,
                'urlRedirection' => $this->generateUrl('extranet_questionnaires_questionnaires')
            )
        );
    }

    /**
     * Affiche la page avec le formulaire de droit de réponse pour ajout
     *
     * @Route("/droit-de-reponse/ajout/{qid}/{qrid}", requirements={"qid" = "\d+", "qrid" = "\d+"},
     *     name="extranet_questionnaires_droit_de_reponse_ajout")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param int $qid Identifiant du questionnaire répondu
     * @param int $qrid Identifiant de la réponse pour laquelle on souhaite effectuer le droit de réponse
     *
     * @return Response
     *
     * @throws Exception Si on trouve des incohérences dans la vérification des arguments d'appel
     */
    public function droitDeReponseAjoutAction(Request $request, $qid, $qrid)
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);
        
        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        
        if (!$this->get('sceau.questionnaire_repondu')
            ->coherenceArgumentsDroitDeReponseAjout($site, $qid, $qrid)) {
            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
            );
        }

        $questionnaire = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')->find($qid);
        $questionnaireReponse = $this->getDoctrine()
            ->getRepository('SceauBundle:QuestionnaireReponse')->find($qrid);
        
        // ToDo : Si le droit de réponse existe déjà pour la réponse et qu'il est actif, on redirige vers la page de modification du dit droit de réponse
        /*
            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_droit_de_reponse_modification', array('qid' => $qid, 'qrid' => $qrid, 'drid' => $drid,))
            );
        */

        $infosGeneralesQuestionnaire = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // Construction du formulaire pour le droit de réponse
        $droitDeReponse = new DroitDeReponse();
        $formBuilder = $this->get('form.factory')->createBuilder('form', $droitDeReponse);

        $translator = $this->get('translator');

        $formBuilder->add('commentaire', 'textarea', array('trim' => true))
                ->add('valider', 'submit', array('label' => $translator->trans('commun_valider', array(), 'commun')));

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // ToDo : il faudra effectuer des checks complets de saisie par la suite (trim, nombre de caractères minimum,
        // mots interdits, mots longs, caractères répétés, etc.)
        if ($form->isValid()) {
            try {
                $droitDeReponse->setQuestionnaireReponse($questionnaireReponse);

                $em = $this->getDoctrine()->getManager();
                $em->persist($droitDeReponse);
                $em->flush();
            } catch (Exception $e) {
                $request->getSession()->getFlashBag()->add(
                    'droit_de_reponse_erreur',
                    $translator->trans('probleme_technique', array(), 'erreurs')
                );

                return $this->redirect(
                    $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
                );
            }

            // ToDo : envoyer un e-mail à l'internaute pour informer du droit de réponse. L'e-mail contiendra un lien vers la fiche marchand avec ancre vers le commentaire. cf. existant Sceau v2

            $request->getSession()->getFlashBag()->add(
                'droit_de_reponse_succes',
                $translator->trans('message_creation_droit_de_reponse_succes', array(), 'extranet_droit_de_reponse')
            );

            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
            );
        }

        return $this->render(
            'SceauBundle:Extranet/Questionnaires:droit_de_reponse.html.twig',
            array(
                'nbCaracteresMax' => $this->container->getParameter('nb_caracteres_max_droit_de_reponse'),
                'form' => $form->createView(),
                'infosGeneralesQuestionnaire' => $infosGeneralesQuestionnaire,
                'questionnaireReponse' => $questionnaireReponse,
                'droitDeReponse_id' => 0,
                'parametrage' => $questionnaireType->getParametrage(),
                'urlRedirection' => $this->generateUrl(
                    'extranet_questionnaires_detail_questionnaire',
                    array('questionnaire_id' => $qid)
                )
            )
        );
    }

    /**
     * Affiche la page avec le formulaire de droit de réponse pour modification
     *
     * @Route("/questionnaires/droit-de-reponse/modification/{qid}/{qrid}/{drid}", requirements={"qid" = "\d+", "qrid" = "\d+", "drid" = "\d+"},
     *     name="extranet_questionnaires_droit_de_reponse_modification")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param int $qid Identifiant du questionnaire répondu
     * @param int $qrid Identifiant de la réponse pour laquelle on souhaite effectuer le droit de réponse
     * @param int $drid Identifiant du droit de réponse
     *
     * @return Response
     *
     * @throws Exception Si on trouve des incohérences dans la vérification des arguments d'appel
     */
    public function droitDeReponseModificationAction(Request $request, $qid, $qrid, $drid)
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');

        if (!$this->get('sceau.questionnaire_repondu')
            ->coherenceArgumentsDroitDeReponseModification($site, $qid, $qrid, $drid)) {
            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
            );
        }

        $questionnaire = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')->find($qid);
        $questionnaireReponse = $this->getDoctrine()
            ->getRepository('SceauBundle:QuestionnaireReponse')->find($qrid);

        $infosGeneralesQuestionnaire = $this->getDoctrine()->getRepository('SceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);

        // Construction du formulaire pour le droit de réponse
        $em = $this->getDoctrine()->getManager();

        $droitDeReponse = $em->getRepository('SceauBundle:DroitDeReponse')->find($drid);

        $formBuilder = $this->get('form.factory')->createBuilder('form', $droitDeReponse);

        $translator = $this->get('translator');

        $formBuilder->add('commentaire', 'textarea', array('trim' => true))
                ->add('valider', 'submit', array('label' => $translator->trans('commun_valider', array(), 'commun')));

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // ToDo : il faudra effectuer des checks complets de saisie par la suite (trim, nombre de caractères minimum,
        // mots interdits, mots longs, caractères répétés, etc.)
        if ($form->isValid()) {
            
            try {
                $droitDeReponse->setQuestionnaireReponse($questionnaireReponse);

                $em->persist($droitDeReponse);
                $em->flush();
            } catch (Exception $e) {
                $request->getSession()->getFlashBag()->add(
                    'droit_de_reponse_erreur',
                    $translator->trans('probleme_technique', array(), 'erreurs')
                );

                return $this->redirect(
                    $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
                );
            }

            $request->getSession()->getFlashBag()->add(
                'droit_de_reponse_succes',
                $translator->trans('message_creation_droit_de_reponse_succes', array(), 'extranet_droit_de_reponse')
            );

            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
            );
        }
        
        return $this->render(
            'SceauBundle:Extranet/Questionnaires:droit_de_reponse.html.twig',
            array(
                'nbCaracteresMax' => $this->container->getParameter('nb_caracteres_max_droit_de_reponse'),
                'form' => $form->createView(),
                'infosGeneralesQuestionnaire' => $infosGeneralesQuestionnaire,
                'questionnaireReponse' => $questionnaireReponse,
                'droitDeReponse_id' => $droitDeReponse->getId(),
                'parametrage' => $questionnaireType->getParametrage(),
                'urlRedirection' => $this->generateUrl(
                    'extranet_questionnaires_detail_questionnaire',
                    array('questionnaire_id' => $qid)
                )
            )
        );
    }

    /**
     * Supprime le droit de réponse
     *
     * @Route("/questionnaires/droit-de-reponse/suppression/{qid}/{qrid}/{drid}", requirements={"qid" = "\d+", "qrid" = "\d+", "drid" = "\d+"},
     *     name="extranet_questionnaires_droit_de_reponse_suppression")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param int $qid Identifiant du questionnaire répondu
     * @param int $qrid Identifiant de la réponse pour laquelle on souhaite effectuer le droit de réponse
     * @param int $drid Identifiant du droit de réponse
     *
     * @return Response
     *
     * @throws Exception Si on trouve des incohérences dans la vérification des arguments d'appel
     */
    public function droitDeReponseSuppressionAction(Request $request, $qid, $qrid, $drid)
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');

        if (!$this->get('sceau.questionnaire_repondu')
            ->coherenceArgumentsDroitDeReponseModification($site, $qid, $qrid, $drid)) {
            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
            );
        }

        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $droitDeReponse = $em
            ->getRepository('SceauBundle:DroitDeReponse')
            ->find($drid);

        try {
            $em->remove($droitDeReponse);
            $em->flush();
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add(
                'droit_de_reponse_erreur',
                $translator->trans('probleme_technique', array(), 'erreurs')
            );

            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
            );
        }

        $request->getSession()->getFlashBag()->add(
            'droit_de_reponse_succes',
            $translator->trans('message_suppression_droit_de_reponse_succes', array(), 'extranet_droit_de_reponse')
        );

        return $this->redirect(
            $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
        );

    }

    /**
     * @Route("/import", name="extranet_questionnaires_import")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function importAction(Request $request)
    {
        $form = $this->createForm(new QuestionnairesImportType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message  = 'import.error';
            $filename = $form->get('filename')->getData();

            $em = $this->get('doctrine.orm.entity_manager');
            /** @var \SceauBundle\Entity\Site $site */
            $site = $em->merge($request->getSession()->get('siteSelectionne'));
            /** @var \SceauBundle\Entity\QuestionnaireType $questionnaireType */
            $questionnaireType = $em->merge($request->getSession()->get('questionnaireTypeSelectionne'));

            /** @var \SceauBundle\Entity\Site $siteInfo */
            $siteInfo = $this->getDoctrine()->getRepository('Site')
                ->parametragesCSVManuelByQuestionnaireType($site->getId(), $questionnaireType->getId())
            ;

            /** @var \SceauBundle\Entity\QuestionnairePersonnalisation $questionnairePersonnalisation */
            if ($siteInfo && ($questionnairePersonnalisation = $siteInfo->getQuestionnairePersonnalisations()[0])) {
                $filepath = $questionnairePersonnalisation->getCommandeCSVParametrage()->getDossierStockage();
                if ($filepath) {
                    $form->get('file')->getData()->move($filepath, $filename);
                    $message = 'import.success';
                }
            }

            $this->addFlash($message, $this->get('translator')->trans($message, [], 'extranet_questionnaires_import'));
        }

        return $this->render('@Sceau/Extranet/Questionnaires/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
