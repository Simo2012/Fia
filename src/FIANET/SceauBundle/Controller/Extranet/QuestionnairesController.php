<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use DateInterval;
use DateTime;
use Exception;
use FIANET\SceauBundle\Entity\DroitDeReponse;
use FIANET\SceauBundle\Entity\Langue;
use FIANET\SceauBundle\Entity\Question;
use FIANET\SceauBundle\Entity\QuestionType;
use FIANET\SceauBundle\Entity\Relance;
use FIANET\SceauBundle\Exception\Extranet\AccesInterditException;
use FIANET\SceauBundle\Form\Type\RelanceType;
use FIANET\SceauBundle\Form\Type\Extranet\QuestionnairesListeType;
use FIANET\SceauBundle\Form\Type\SelectLangueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class QuestionnairesController extends Controller
{
    /**
     * Affiche la page de listing des questionnaires. Les questionnaires affichés dépendent des filtres et tris demandés
     * par l'utilisateur.
     *
     * @Route("/questionnaires/questionnaires", name="extranet_questionnaires_questionnaires")
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

        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

        $form = $this->createForm(new QuestionnairesListeType(), null);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            /* Affichage de la page sans soumission du formulaire : on récupère les éventuels paramètres de
            recherche sauvegardés dans les cookies. */
            $tri = 2;
            $dateDebut = $request->cookies->get('questionnaires_dateDebut');
            $form->get('dateDebut')->setData($dateDebut);
            $dateFin = $request->cookies->get('questionnaires_dateFin');
            $form->get('dateFin')->setData($dateFin);
            $indicateurs = ($request->cookies->get('questionnaires_indicateurs')) ?
                explode('-', $request->cookies->get('questionnaires_indicateurs')) : array();
            $form->get('indicateurs')->setData($indicateurs);
            $recherche = $request->cookies->get('questionnaires_recherche');
            $form->get('recherche')->setData($recherche);
            $litige = $request->cookies->get('questionnaires_litige') ? true : null;
            $form->get('litige')->setData($litige);

            if ($questionnaireType->getParametrage()['livraison']) {
                if ($request->cookies->get('questionnaires_livraison')) {
                    $livraisonType = $this->getDoctrine()->getRepository('FIANETSceauBundle:LivraisonType')
                        ->find($request->cookies->get('questionnaires_livraison'));
                    $livraison = $livraisonType->getId();

                    $form->get('livraison')->setData($livraisonType);
                } else {
                    $livraisonType = null;
                    $livraison = '';
                }
            } else {
                $livraisonType = null;
                $livraison = '';
            }

            $retenir = false;

        } else {
            /* Soumission du formulaire */
            $donneesForm = $form->getData();
            $tri = is_numeric($donneesForm['tri']) ? $donneesForm['tri'] : 2;
            $dateDebut = (isset($donneesForm['dateDebut'])) ? $donneesForm['dateDebut'] : '';
            $dateFin = (isset($donneesForm['dateFin'])) ? $donneesForm['dateFin'] : '';
            $indicateurs = (isset($donneesForm['indicateurs'])) ? $donneesForm['indicateurs'] : array();
            $recherche = (isset($donneesForm['recherche'])) ? $donneesForm['recherche'] : '';
            $litige = $donneesForm['litige'] ? true : null;

            $livraisonType = $questionnaireType->getParametrage()['livraison'] ?
                $donneesForm['livraison'] : null;
            $livraison = $livraisonType ? $livraisonType->getId() : '';

            $retenir = $donneesForm['retenir'];
        }

        if (!$form->isSubmitted() || ($form->isSubmitted() && $form->isValid())) {
            /* Affichage de la page sans soumission du formulaire ou formulaire soumis et valide */

            $site = $request->getSession()->get('siteSelectionne');

            $listeReponsesIndicateurs = $this->get('fianet_sceau.notes')
                ->listeReponsesIndicateursPourQuestionnaireType($questionnaireType, $indicateurs);

            $nbTotalQuestionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                ->nbTotalQuestionnaires(
                    $site,
                    $questionnaireType,
                    $dateDebut,
                    $dateFin,
                    $recherche,
                    $listeReponsesIndicateurs,
                    $livraisonType
                );

            $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                ->listeQuestionnaires(
                    $site,
                    $questionnaireType,
                    $dateDebut,
                    $dateFin,
                    $recherche,
                    $listeReponsesIndicateurs,
                    $livraisonType,
                    0,
                    $nbQuestionnairesMax,
                    $tri
                );
        } else {
            /* Formulaire soumis et non valide */
            $nbTotalQuestionnaires = 0;
            $questionnaires = array();
        }

        $reponse =  $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:questionnaires.html.twig',
            array(
                'nbTotalQuestionnaires' => $nbTotalQuestionnaires,
                'questionnaires' => $questionnaires,
                'nbQuestionnairesMax' => $nbQuestionnairesMax,
                'form' => $form->createView(),
                'offset' => 0,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'tri' => $tri,
                'recherche' => $recherche,
                'indicateurs' => implode('-', $indicateurs),
                'livraison' => $livraison,
                'parametrage' => $questionnaireType->getParametrage(),
                'urlRedirection' => $this->generateUrl('extranet_questionnaires_questionnaires', array(), true)
            )
        );

        /* Sauvegarde des paramètres de recherche */
        if ($retenir) {
            $dateExpiration = new DateTime();
            $dateExpiration->add(new DateInterval('P6M'));

            $reponse->headers->setCookie(new Cookie('questionnaires_dateDebut', $dateDebut, $dateExpiration));
            $reponse->headers->setCookie(new Cookie('questionnaires_dateFin', $dateFin, $dateExpiration));
            $reponse->headers->setCookie(
                new Cookie(
                    'questionnaires_indicateurs',
                    implode('-', $indicateurs),
                    $dateExpiration
                )
            );
            $reponse->headers->setCookie(new Cookie('questionnaires_recherche', $recherche, $dateExpiration));
            $reponse->headers->setCookie(new Cookie('questionnaires_litige', $litige, $dateExpiration));

            if ($questionnaireType->getParametrage()['livraison']) {
                if ($livraisonType) {
                    $reponse->headers->setCookie(
                        new Cookie(
                            'questionnaires_livraison',
                            $livraisonType->getId(),
                            $dateExpiration
                        )
                    );
                } else {
                    $reponse->headers->clearCookie('questionnaires_livraison');
                }
            }
            
            $reponse->headers->setCookie(new Cookie('questionnaires_tri', $tri, $dateExpiration));
        }

        return $reponse;
    }

    /**
     * Action appelable uniquement via AJAX. Elle retourne les questionnaires par paquet de lignes de tableau en
     * fonction des filtres demandés (utilisé pour le scroll infini).
     *
     * @Route("/questionnaires/questionnaires-ajax", name="extranet_questionnaires_questionnaires_ajax",
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
                $livraisonType = $this->getDoctrine()->getRepository('FIANETSceauBundle:LivraisonType')
                    ->find($request->request->get('livraison'));
            } else {
                $livraisonType = null;
            }

            $listeReponsesIndicateurs = $this->get('fianet_sceau.notes')
                ->listeReponsesIndicateursPourQuestionnaireType($questionnaireType, $indicateurs);

            $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
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
                'FIANETSceauBundle:Extranet/Questionnaires:questionnaires_lignes.html.twig',
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
     * @Route("/questionnaires/questions-personnalisees", name="extranet_questionnaires_questions_personnalisees")
     * @Route("/questionnaires/questions-personnalisees/{id}",
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
        $elementMenu = $this->get('fianet_sceau.extranet.menu')
            ->getChild('questionnaires')->getChild('questionnaires.questions_personnalisees');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $site = $em->merge($request->getSession()->get('siteSelectionne'));
        $questionnaireType = $em->merge($request->getSession()->get('questionnaireTypeSelectionne'));
        $questionsEnAttenteDeValidation = $em->getRepository('FIANETSceauBundle:Question')
            ->questionsPersosEnAttenteDeValidation($site, $questionnaireType);

        $question = new Question();
        $question->setSite($site);
        $question->addQuestionnaireType($questionnaireType);
        if ($questionType) {
            $question->setQuestionType($questionType);
        }

        $form = $this->createForm('fianet_sceaubundle_question_perso', $question);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->get('fianet_sceau.questionnaire_structure')->ajouterQuestionPerso($question);

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
            'FIANETSceauBundle:Extranet/Questionnaires:question_perso.html.twig',
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
     * @Route("/questionnaires/relance-questionnaires", name="extranet_questionnaires_relance_questionnaires")
     * @Route("/questionnaires/relance-questionnaires/{langue_id}", requirements={"langue_id" = "\d+"},
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
        $menu = $this->get('fianet_sceau.extranet.menu');
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        $em = $this->getDoctrine()->getManager();

        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        $site = $request->getSession()->get('siteSelectionne');
        $datePeriode = $this->get('fianet_sceau.relance')->calculerPeriode();
        $nbQuestionnairesMax = $this->container->getParameter('nb_relances_max');

        $form = $this->createForm(new SelectLangueType());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $langue = $form->get('langue')->getData();

        } else {
            if ($langue_id) {
                $langue = $em->getRepository('FIANETSceauBundle:Langue')->langueViaId($langue_id);

            } else {
                $langue = $em->getRepository('FIANETSceauBundle:Langue')
                    ->langueViaCode($this->container->getParameter('langue_par_defaut'));
            }

            $form->get('langue')->setData($langue);
        }

        $nbTotalQuestionnaires = $em->getRepository('FIANETSceauBundle:Questionnaire')
            ->nbTotalQuestionnairesARelancer(
                $site,
                $questionnaireType,
                $datePeriode['dateDebut'],
                $datePeriode['dateFin'],
                $langue->getId()
            );

        $questionnaires = $em->getRepository('FIANETSceauBundle:Questionnaire')
            ->listeQuestionnairesARelancerParPaquet(
                $site,
                $questionnaireType,
                $datePeriode['dateDebut'],
                $datePeriode['dateFin'],
                $langue->getId(),
                0,
                $nbQuestionnairesMax
            );

        $relanceValidee = $em->getRepository('FIANETSceauBundle:Relance')
            ->relanceValidee($site, $questionnaireType, $langue);
        if ($relanceValidee && $relanceValidee->getAuto()) {
            $auto = true;
        } else {
            $auto = false;
        }

        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:relance.html.twig',
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
     * @Route("/questionnaires/relance-questionnaires-ajax", name="extranet_questionnaires_relance_ajax",
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
            $datePeriode = $this->get('fianet_sceau.relance')->calculerPeriode();

            $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
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
                'FIANETSceauBundle:Extranet/Questionnaires:relance_lignes.html.twig',
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
     * @Route("/questionnaires/relance-auto", name="extranet_questionnaires_relance_auto",
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
            $gestionRelance = $this->get('fianet_sceau.relance');

            $langue = $this->getDoctrine()->getRepository('FIANETSceauBundle:Langue')
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
     * @Route("/questionnaires/renvoyer/{langue_id}", requirements={"langue_id" = "\d+"},
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

        if ($this->get('fianet_sceau.relance')->renvoyerQuestionnaires(
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
     * @Route("/questionnaires/personnaliser-relance/{langue_id}", requirements={"langue_id" = "\d+"},
     *     name="extranet_questionnaires_relance_perso")
     * @ParamConverter("langue", class="FIANETSceauBundle:Langue", options={"id" = "langue_id"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param Langue $langue Instance de Langue
     *
     * @return Response Instance de Response
     */
    public function relancePersonnaliserEmailAction(Request $request, Langue $langue)
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
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
            if ($this->get('fianet_sceau.relance')->creer($site, $questionnaireType, $langue, $form->getData())) {
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
            'FIANETSceauBundle:Extranet/Questionnaires:relance_perso.html.twig',
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
     * @Route("/questionnaires/detail-questionnaire/{questionnaire_id}", requirements={"id" = "\d+"},
     *     name="extranet_questionnaires_detail_questionnaire")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param int $questionnaire_id Identifiant du questionnaire
     *
     * @return Response Instance de Response
     *
     * @throws Exception
     */
    public function detailQuestionnaireAction(Request $request, $questionnaire_id)
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));

        if (!$this->getDoctrine()->getManager()->getRepository('FIANETSceauBundle:Questionnaire')
            ->verifierExistenceEtLiaisonAvecSite($questionnaire_id, $site)) {
            throw new Exception($this->get('translator')->trans('questionnaire_invalide', array(), 'erreurs'));
        }

        $questionnaires = $this->get('fianet_sceau.questionnaire_repondu')
            ->recupStructureQuestionnaireAvecReponses($site, $questionnaire_id);

        /* TODO : rajouter la pagination */

        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:detail_questionnaire.html.twig',
            array(
                'questionnaire1' => $questionnaires['questionnaire1'],
                'questionnaire2' => $questionnaires['questionnaire2'],
                'parametrage' => $questionnaires['questionnaire1']->getQuestionnaireType()->getParametrage(),
                'urlRedirection' => $this->generateUrl('extranet_questionnaires_questionnaires')
            )
        );
    }

    /**
     * Affiche la page avec le formulaire de droit de réponse pour ajout
     *
     * @Route("/questionnaires/droit-de-reponse/ajout/{qid}/{qrid}", requirements={"qid" = "\d+", "qrid" = "\d+"},
     *     name="extranet_questionnaires_droit_de_reponse_ajout")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response
     *
     * @throws Exception Si on trouve des incohérences dans la vérification des arguments d'appel
     */
    public function droitDeReponseAjoutAction(Request $request, $qid, $qrid)
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);
        
        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        
        if (!$this->get('fianet_sceau.questionnaire_repondu')
            ->coherenceArgumentsDroitDeReponseAjout($site, $qid, $qrid)) {
            return $this->redirect(
                $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('questionnaire_id' => $qid))
            );
        }
        
        $questionnaire = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')->find($qid);
        $questionnaireReponse = $this->getDoctrine()
            ->getRepository('FIANETSceauBundle:QuestionnaireReponse')->find($qrid);
        
        $infosGeneralesQuestionnaire = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // Construction du formulaire pour le droit de réponse
        $droitDeReponse = new DroitDeReponse();
        $formBuilder = $this->get('form.factory')->createBuilder('form', $droitDeReponse);

        $formBuilder->add('commentaire', 'textarea', array('trim' => true))
                ->add('valider', 'submit');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // ToDo : il faudra effectuer des checks complets de saisie par la suite (trim, nombre de caractères minimum,
        // mots interdits, mots longs, caractères répétés, etc.)
        if ($form->isValid()) {
            $droitDeReponse->setQuestionnaireReponse($questionnaireReponse);

            $em = $this->getDoctrine()->getManager();
            $em->persist($droitDeReponse);
            $em->flush();

            // ToDo : gérer l'affichage du message flash dans le template
            $request->getSession()->getFlashBag()->add('notice', 'Droit de réponse bien enregistré.');

            // On redirige vers la page de visualisation de listing des questionnaires une fois le message flash affiché
            // ToDo : mettre la condition avant redirection
            return $this->redirect($this->generateUrl('extranet_questionnaires_questionnaires'));
            
            // Pour retrouner sur le détail de questionnaire
            // return $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('id' => $qid);
        }
        
        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:droit_de_reponse.html.twig',
            array(
                'nbCaracteresMax' => $this->container->getParameter('nb_caracteres_max_droit_de_reponse'),
                'form' => $form->createView(),
                'infosGeneralesQuestionnaire' => $infosGeneralesQuestionnaire,
                'questionnaireReponse' => $questionnaireReponse,
                'parametrage' => $questionnaireType->getParametrage(),
                'urlRedirection' => $this->generateUrl(
                    'extranet_questionnaires_detail_questionnaire',
                    array('questionnaire_id' => $qid)
                )
            )
        );
    }
}
