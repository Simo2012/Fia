<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use DateInterval;
use DateTime;
use Exception;
use FIANET\SceauBundle\Entity\DroitDeReponse;
use FIANET\SceauBundle\Entity\Langue;
use FIANET\SceauBundle\Entity\Relance;
use FIANET\SceauBundle\Exception\Extranet\AccesInterditException;
use FIANET\SceauBundle\Form\RelanceType;
use FIANET\SceauBundle\Form\Type\Extranet\QuestionnairesListeType;
use FIANET\SceauBundle\Form\Type\SelectLangueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                ->getReponsesIDIndicateursPourQuestionnaireType($questionnaireType, $indicateurs);

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
                'parametrageIndicateur' => $questionnaireType->getParametrage()['indicateur'],
                'parametrageRecommendation' => $questionnaireType->getParametrage()['recommandation'],
                'parametrageLibelleCommandeDate' => $questionnaireType->getParametrage()['libelleCommandeDate'],
                'parametrageLivraison' => $questionnaireType->getParametrage()['livraison'],
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
                ->getReponsesIDIndicateursPourQuestionnaireType($questionnaireType, $indicateurs);

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
                    'parametrageIndicateur' => $questionnaireType->getParametrage()['indicateur'],
                    'parametrageRecommendation' => $questionnaireType->getParametrage()['recommandation']
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
     *
     * @Route("/questionnaires/questions-personnalisées", name="extranet_questionnaires_questions_personnalisees")
     * @Method("GET")
     */
    public function x2Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');

        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.questions_personnalisees');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        return $this->render('FIANETSceauBundle:Extranet/Questionnaires:index.html.twig');
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
                'delaiJoursRelance' => $this->container->getParameter('delaiJoursRelance'),
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
     * @Route("/questionnaires/detail-questionnaire/{id}", requirements={"id" = "\d+"}, name="extranet_questionnaires_detail_questionnaire")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     * @param Integer $id Identifiant du type de questionnaire
     *
     * @return Response Instance de Response
     */
    public function detailQuestionnaireAction(Request $request, $id)
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);
        
        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));

        if (!$this->get('fianet_sceau.questionnaire_repondu')->coherenceArgumentsDetailsQuestionnaire($site, $id)) {
            throw new Exception('Questionnaire invalide');
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $questionnaire_param = $em->getRepository('FIANETSceauBundle:Questionnaire')->find($id);
        $questionnaireType_param = $questionnaire_param->getQuestionnaireType();
        
        /* On récupère les éventuels paramètres de recherche sauvegardés dans les cookies. */
        $dateDebut = $request->cookies->get('questionnaires_dateDebut');
        $dateFin = $request->cookies->get('questionnaires_dateFin');
        $recherche = $request->cookies->get('questionnaires_recherche');
        $indicateurs = ($request->cookies->get('questionnaires_indicateurs')) ?
            explode('-', $request->cookies->get('questionnaires_indicateurs')) : array();
        $listeReponsesIndicateurs = $this->get('fianet_sceau.notes')
                ->getReponsesIDIndicateursPourQuestionnaireType($questionnaireType_param, $indicateurs);
        
        if ($questionnaireType_param->getParametrage()['livraison']) {
            if ($request->cookies->get('questionnaires_livraison')) {
                $livraisonType = $this->getDoctrine()->getRepository('FIANETSceauBundle:LivraisonType')
                    ->find($request->cookies->get('questionnaires_livraison'));
            } else {
                $livraisonType = null;
            }
        } else {
            $livraisonType = null;
        }
        $tri = ($request->cookies->get('questionnaires_tri')) ? $request->cookies->get('questionnaires_tri') : 2;
        
        /* ToDo : on devra ajouter/gérer le filtre avec les litiges dans un prochain lot */        
        
        $navigation = $this->get('fianet_sceau.questionnaire_repondu')->getNavigation($site,
                    $questionnaireType_param,
                    $dateDebut,
                    $dateFin,
                    $recherche,
                    $listeReponsesIndicateurs,
                    $livraisonType,
                    $questionnaire_param,
                    $tri);
        
        $questionnaire = $this->get('fianet_sceau.questionnaire_repondu')->getQuestionnairePrincipal($questionnaire_param);    
        $questionnaireType = $questionnaire->getQuestionnaireType();
        
        // On récupère toutes les informations générales liées au questionnaire
        $infosGeneralesQuestionnaire = $em->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // On récupère toutes les informations liées au questionnaire répondu
        // 1. Gestion du titre de bloc selon le type du questionnaire
        $questionnaireTypeLibelle = $this->get('fianet_sceau.questionnaire_repondu')->getLibelleQuestionnaireTypeRepondu($questionnaireType);
        
        // 2. Récupération des questions et réponses (questions communes, questions personnalisées, etc.)
        $questionnaireListeQuestionsReponses = $this->get('fianet_sceau.questionnaire_repondu')->getAllQuestionsReponses($questionnaire, $questionnaireType);
        
        // 3. On récupère les données du questionnaire lié si ce dernier existe
        $questionnaireSuivant = false;
        $questionnaireLieSuivant = null;        
        $questionnaireLieSuivantType = null;
        $questionnaireLieSuivantTypeLibelle = null;
        $questionnaireLieSuivantListeQuestionsReponses = null;
        $infosGeneralesQuestionnaireLieSuivant = null;
        $questionnaireLieSuivantMsg = array();
        
        if ($questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant()) {
            
            $questionnaireSuivant = true;
            $questionnaireLieSuivantType = $questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant();
            $questionnaireLieSuivant = $this->get('fianet_sceau.questionnaire_repondu')->getQuestionnaireLieSuivant($questionnaire);
                        
            $questionnaireLieSuivantTypeLibelle = $this->get('fianet_sceau.questionnaire_repondu')->getLibelleQuestionnaireTypeRepondu($questionnaireLieSuivantType);            
            
            if ($questionnaireLieSuivant) {
                $questionnaireLieSuivantListeQuestionsReponses = $this->get('fianet_sceau.questionnaire_repondu')->getAllQuestionsReponses($questionnaireLieSuivant, $questionnaireLieSuivantType);
                $infosGeneralesQuestionnaireLieSuivant = $em->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaireLieSuivant, $questionnaireLieSuivantType);
            } else {
                $questionnaireLieSuivantMsg = $this->get('fianet_sceau.questionnaire_repondu')->getMsgQuestionnaireLieSuivant($questionnaire);
            }
            
        }
        
        $parametrageIndicateur = (isset($questionnaireType->getParametrage()['indicateur'])) ?  $questionnaireType->getParametrage()['indicateur'] : null;
        
        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:detail_questionnaire.html.twig', array(
                'questionnaire' => $infosGeneralesQuestionnaire,
                'questionnaireTypeLibelle' => $questionnaireTypeLibelle,
                'questionnaireListeQuestionsReponses' => $questionnaireListeQuestionsReponses,
                'questionnaireSuivant' => $questionnaireSuivant,
                'questionnaireLieSuivant' => $infosGeneralesQuestionnaireLieSuivant,
                'questionnaireLieSuivantTypeLibelle' => $questionnaireLieSuivantTypeLibelle,
                'questionnaireLieSuivantListeQuestionsReponses' => $questionnaireLieSuivantListeQuestionsReponses,
                'questionnaireLieSuivantMsg' => $questionnaireLieSuivantMsg,
                'navigation' => $navigation,
                'parametrageIndicateur' => $parametrageIndicateur,
                'urlRedirection' => $this->generateUrl('extranet_questionnaires_questionnaires', array(), true)
            )
        );
        
    }    
    
    /**
     * Affiche la page avec le formulaire de droit de réponse pour ajout
     * 
     * @Route("/questionnaires/droit-de-reponse/ajout/{qid}/{qrid}", requirements={"qid" = "\d+", "qrid" = "\d+"}, name="extranet_questionnaires_droit_de_reponse_ajout")
     * @Method({"GET", "POST"})
     * 
     * @param Request $request Instance de Request
     *
     * @return Response
     * 
     * 
     * @throws Exception Si on trouve des incohérences dans la vérification des arguments d'appel
     */
    public function droitDeReponseAjoutAction(Request $request, $qid, $qrid) {
        
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);
        
        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));        
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        
        if (!$this->get('fianet_sceau.questionnaire_repondu')->coherenceArgumentsDroitDeReponseAjout($site, $qid, $qrid)) {
            return $this->redirect($this->generateUrl('extranet_questionnaires_detail_questionnaire', array('id' => $qid)));
        }
        
        $questionnaire = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')->find($qid);
        $questionnaireReponse = $this->getDoctrine()->getRepository('FIANETSceauBundle:QuestionnaireReponse')->find($qrid);
        
        $infosGeneralesQuestionnaire = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // Construction du formulaire pour le droit de réponse
        $droitDeReponse = new DroitDeReponse();
        $formBuilder = $this->get('form.factory')->createBuilder('form', $droitDeReponse);

        $formBuilder
                ->add('commentaire', 'textarea', array(
                    'trim' => true))
                ->add('valider', 'submit')      
        ;

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        
        // ToDo : il faudra effectuer des checks complets de saisie par la suite (trim, nombre de caractères minimum, mots interdits, mots longs, caractères répétés, etc.)
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
            'FIANETSceauBundle:Extranet/Questionnaires:droit_de_reponse.html.twig', array(
                'nbCaracteresMax' => $this->container->getParameter('nb_caracteres_max_droit_de_reponse'),
                'form' => $form->createView(),
                'infosGeneralesQuestionnaire' => $infosGeneralesQuestionnaire,
                'questionnaireReponse' => $questionnaireReponse,
                'parametrageIndicateur' => $questionnaireType->getParametrage()['indicateur'],
                'urlRedirection' => $this->generateUrl('extranet_questionnaires_detail_questionnaire', array('id' => $qid))
            )
        );
    }
    
}
