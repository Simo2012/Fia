<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use DateInterval;
use DateTime;
use Exception;
use FIANET\SceauBundle\Entity\DroitDeReponse;
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

            $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));

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
                'parametrageLivraison' => $questionnaireType->getParametrage()['livraison']
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
                    $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne')),
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
     * Affiche la page de relance des questionnaires.
     *
     * @Route("/questionnaires/relance-questionnaires", name="extranet_questionnaires_relance_questionnaires")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response Instance de Response
     */
    public function relanceAction(Request $request)
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
        $datePeriode = $this->get('fianet_sceau.relance')->calculerPeriode();
        $nbQuestionnairesMax = $this->container->getParameter('nb_relances_max');

        $form = $this->createForm(new SelectLangueType());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $langue = $form->get('langue')->getData();

        } else {
            $langue = $this->getDoctrine()->getRepository('FIANETSceauBundle:Langue')
                ->findOneByCode($this->container->getParameter('langue_par_defaut'));
            $form->get('langue')->setData($langue);
        }

        $nbTotalQuestionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
            ->nbTotalQuestionnairesARelancer(
                $site,
                $questionnaireType,
                $datePeriode['dateDebut'],
                $datePeriode['dateFin'],
                $langue->getId()
            );

        $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
            ->listeQuestionnairesARelancer(
                $site,
                $questionnaireType,
                $datePeriode['dateDebut'],
                $datePeriode['dateFin'],
                $langue->getId(),
                0,
                $nbQuestionnairesMax
            );

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
                'templateEmail' => $questionnaireType->getParametrage()['templateEmail']
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
                ->listeQuestionnairesARelancer(
                    $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne')),
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
    public function relanceAutomatisationAjax(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));
            $gestionRelance = $this->get('fianet_sceau.relance');

            $langue = $this->getDoctrine()->getRepository('FIANETSceauBundle:Langue')
                ->find($request->request->get('langue_id'));
            $activer = $request->request->get('activer');

            if ($activer) {
                $gestionRelance->automatiser($site, $langue);
            } else {
                $gestionRelance->desautomatiser($site, $langue);
            }

            return new JsonResponse(true);

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
     * Affiche la page qui permet de personnaliser la relance pour un type de questionnaire.
     *
     * @Route("/questionnaires/personnaliser-relance/{langue_id}", requirements={"id" = "\d+"},
     *     name="extranet_questionnaires_perso_relance")
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response Instance de Response
     */
    public function personnaliserEmailRelance(Request $request, $langue_id)
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        $site = $this->getDoctrine()->getManager()->merge($request->getSession()->get('siteSelectionne'));

        $relance = new Relance();
        $form = $this->createForm(new RelanceType(), $relance);

        if ($form->isValid()) {
            echo 'oki';
        }

        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:relance_perso.html.twig',
            array(
                'templateEmail' => $questionnaireType->getParametrage()['templateEmail'],
                'objetParDefaut' => sprintf(
                    $this->container->getParameter('relance_objet_par_defaut'),
                    $site->getNom()
                ),
                'form' => $form->createView()
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
        //$questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        
        if (!$this->get('fianet_sceau.questionnaire_repondu')->coherenceArgumentsDetailsQuestionnaire($site, $id)) {
            throw new Exception('Questionnaire invalide');
        }

        $em = $this->getDoctrine()->getManager();
        $questionnaire = $em->getRepository('FIANETSceauBundle:Questionnaire')->find($id);
        $questionnaireType = $questionnaire->getQuestionnaireType();
        
        // Dans le cas où on appelle un Q2 directement, on doit d'abord aller chercher les infos du Q1
        // ToDo :
        
        // On récupère les informations pour la navigation (questionnaire précédent, questionnaire suivant)
        // La navigation doit se faire :
        //  - selon le filtrage qu'il y a eu sur la page de listing de questionnaire + selon la date de réponse au questionnaire
        //  - OU s'il n'y a pas de filtre, uniquement selon la date de réponse au questionnaire
        
        // -> appel de la méthode récupérant le questionnaire précédent selon les règles ci-dessus
        
        // -> appel de la méthode récupérant le questionnaire suivant selon les règles ci-dessus
        
        // On récupère toutes les informations générales liées au questionnaire
        $infosGeneralesQuestionnaire = $em->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // On récupère toutes les informations liées au questionnaire répondu
        // 1. Gestion du titre de bloc selon le type du questionnaire
        $questionnaireTypeLibelle = $this->get('fianet_sceau.questionnaire_repondu')->getLibelleQuestionnaireTypeRepondu($questionnaireType);
        
        // 2. Récupération des questions et réponses (questions communes, questions personnalisées, etc.)
        $questionnaireListeQuestionsReponses = $this->get('fianet_sceau.questionnaire_repondu')->getAllQuestionsReponses($questionnaire, $questionnaireType);
        
        // ToDo : méthode à créer pour récupérer les infos et modifier l'appel ci-dessous
        //$detailsQuestionnaire = $em->getRepository('FIANETSceauBundle:Questionnaire')
                    //->infosDetailsQuestionnaire($questionnaire, $questionnaireType);
        
        // 3. On récupère les données du questionnaire lié si ce dernier existe
        $questionnaireSuivant = false;
        $questionnaireLie = null;        
        $questionnaireLieType = null;
        $questionnaireLieTypeLibelle = null;
        $questionnaireLieListeQuestionsReponses = null;
        $questionnaireLieCommentairePrincipal = null;
        $infosGeneralesQuestionnaireLie = null;
        
        if ($questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant()) {
            
            $questionnaireSuivant = true;
            $questionnaireLie = $questionnaire->getQuestionnaireLie();
            $questionnaireLieType = $questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant();
            $questionnaireLieTypeLibelle = $this->get('fianet_sceau.questionnaire_repondu')->getLibelleQuestionnaireTypeRepondu($questionnaireLieType);            
            
            // ToDo : dans un lot suivant (car non demandé dans la spec actuelle), on devra gérer les messages dans les cas suivants :
            // - le questionnaire lié n'a pas encore été envoyé "Ce questionnaire n'a pas encore été, ou n'a pu être, envoyé à l'internaute. Envoi prévu pour le 07/05/2015"
            // - le questionnaire lié a été envoyé mais pas encore répondu "Ce questionnaire a été envoyé le 14/04/2015, mais l'internaute n'y a pas encore répondu."
            // - le questionnaire lié a été envoyé mais le délai de réponse est dépassé
            
            if ($questionnaireLie) {
                $questionnaireLieListeQuestionsReponses = $this->get('fianet_sceau.questionnaire_repondu')->getAllQuestionsReponses($questionnaireLie, $questionnaireLieType);
                $infosGeneralesQuestionnaireLie = $em->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaireLie, $questionnaireLieType);
            }
            
        }
        
        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:detail_questionnaire.html.twig', array(
                'questionnaire' => $infosGeneralesQuestionnaire,
                'questionnaireTypeLibelle' => $questionnaireTypeLibelle,
                'questionnaireListeQuestionsReponses' => $questionnaireListeQuestionsReponses,
                'questionnaireSuivant' => $questionnaireSuivant,
                'questionnaireLie' => $infosGeneralesQuestionnaireLie,
                'questionnaireLieTypeLibelle' => $questionnaireLieTypeLibelle,
                'questionnaireLieListeQuestionsReponses' => $questionnaireLieListeQuestionsReponses,
                'parametrageIndicateur' => $questionnaireType->getParametrage()['indicateur']
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
            throw new Exception('Ajout de droit de réponse impossible');
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
        }
        
        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:droit_de_reponse.html.twig', array(
                'nbCaracteresMax' => $this->container->getParameter('nb_caracteres_max_droit_de_reponse'),
                'form' => $form->createView(),
                'infosGeneralesQuestionnaire' => $infosGeneralesQuestionnaire,
                'questionnaireReponse' => $questionnaireReponse,
                'parametrageIndicateur' => $questionnaireType->getParametrage()['indicateur']
            )
        );
    }
    
}
