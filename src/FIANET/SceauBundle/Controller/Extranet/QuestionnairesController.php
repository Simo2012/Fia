<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use DateInterval;
use DateTime;
use Exception;
use FIANET\SceauBundle\Entity\DroitDeReponse;
use FIANET\SceauBundle\Exception\Extranet\AccesInterditException;
use FIANET\SceauBundle\Form\Type\Extranet\QuestionnairesListeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
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
                    $listeReponsesIndicateurs
                );

            $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                ->listeQuestionnaires(
                    $site,
                    $questionnaireType,
                    $dateDebut,
                    $dateFin,
                    $recherche,
                    $listeReponsesIndicateurs,
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
        }

        return $reponse;
    }

    /**
     * Action appelable uniquement via AJAX. Elle retourne les questionnaires par paquet de lignes de tableau en
     * fonction des filtres demandés (utilisé pour le scroll infini).
     *
     * @Route("/questionnaires/questionnaires_ajax", name="extranet_questionnaires_questionnaires_ajax",
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
     *
     * @Route("/questionnaires/relance-questionnaires", name="extranet_questionnaires_relance_questionnaires")
     * @Method("GET")
     */
    public function x3Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');

        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);

        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        return $this->render('FIANETSceauBundle:Extranet/Questionnaires:index.html.twig');
    }
    
    /**
     * Affiche la page de détail d'un questionnaire (avis)
     *
     * @Route("/questionnaires/detail-questionnaire/{id}", name="extranet_questionnaires_detail_questionnaire")
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
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');
        
        $em = $this->getDoctrine()->getManager();
        
        $questionnaire = $em->getRepository('FIANETSceauBundle:Questionnaire')->find($id);
 
        // On vérifie que le questionnaire existe bien
        if ($questionnaire == null) {
            throw $this->createNotFoundException("Le questionnaire n'existe pas.");
        }
        
        // On vérifie que le questionnaire appartient bien au site de la session courante ET qu'il est actif
        if ($questionnaire->getSite()->getId() != $site->getId() || !$questionnaire->getActif()) {
            throw $this->createNotFoundException("Le questionnaire n'existe pas.");
        }
        
        // On récupère les informations pour la navigation (questionnaire précédent, questionnaire suivant)
        // La navigation doit se faire :
        //  - selon le filtrage qu'il y a eu sur la page de listing de questionnaire + selon la date de réponse au questionnaire
        //  - OU s'il n'y a pas de filtre, uniquement selon la date de réponse au questionnaire
        
        // -> appel de la méthode récupérant le questionnaire précédent selon les règles ci-dessus
        
        // -> appel de la méthode récupérant le questionnaire suivant selon les règles ci-dessus
        
        // On récupère toutes les informations générales liées au questionnaire
        $infosGeneralesQuestionnaire = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // On récupère toutes les informations liées au questionnaire répondu
        // 1. Gestion des blocs selon le type du questionnaire (1 ou 2 blocs)
        // 2. Gestion des titres de bloc selon le type du questionnaire (attention: traductibles)
        // 3. Récupération des questions et réponses (questions communes, questions personnalisées, etc.)
        
        // ToDo : méthode à créer pour récupérer les infos et modifier l'appel ci-dessous
        $detailsQuestionnaire = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // On regarde le type de questionnaire, s'il s'agit d'un Q2 on va devoir chercher les infos du Q2 s'il a été répondu
        if ($questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant()) {
            
            // ToDo : méthode à créer pour récupérer les infos et modifier l'appel ci-dessous
            $detailsQuestionnaireSuivant = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
            // ToDo : la méthode doit permettre de retourner les informations suivantes :
            //          - si le Q2 n'a pas encore été envoyé : mettre la date de prévision d'envoi "Ce questionnaire n'a pas encore été, ou n'a pu être, envoyé à l'internaute. Envoi prévu pour le 07/05/2015"
            //          - si le Q2 a été envoyé mais non répondu : mettre l'information du style "Ce questionnaire a été envoyé le 14/04/2015, mais l'internaute n'y a pas encore répondu."
            //          - si le Q2 a été répondu : toutes les informations du questionnaire répondu
        } else {
            $detailsQuestionnaireSuivant = NULL;
        }
        
        return $this->render(
            'FIANETSceauBundle:Extranet/Questionnaires:detail_questionnaire.html.twig', array(
                'infosGeneralesQuestionnaire' => $infosGeneralesQuestionnaire,
                'detailsQuestionnaire' => $detailsQuestionnaire,
                'detailsQuestionnaireSuivant' => $detailsQuestionnaireSuivant,
                'parametrageIndicateur' => $questionnaireType->getParametrage()['indicateur']
            )
        );
        
    }    
    
    /**
     * Affiche la page avec le formulaire de droit de réponse pour ajout
     * 
     * @Route("/questionnaires/droit-de-reponse/ajout", name="extranet_questionnaires_droit_de_reponse_ajout")
     * @Method({"GET", "POST"})
     * 
     * @param Request $request Instance de Request
     *
     * @return Response
     */
    public function droitDeReponseAjoutAction(Request $request) {
        
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);
        
        $questionnaireType = $request->getSession()->get('questionnaireTypeSelectionne');

        $em = $this->getDoctrine()->getManager();

        // NOUVEAU : Le droit de réponse peut être exercé sur n'importe quel champ de type commentaire d'un questionnaire
        // ToDo: on récupèrera l'entité QuestionnaireReponse et non l'id (conversion du paramètre via @ParamConverter)
        $questionnaireReponse_id = is_numeric($request->request->get('questreponse')) ? $request->request->get('questreponse') : 0;
        $questionnaireReponse_id = 1; // pour test     
        if ($questionnaireReponse_id == 0) {
            throw $this->createNotFoundException("Le commentaire n'existe pas.");
        }
        
        $questionnaireReponse = $em
                ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                ->find($questionnaireReponse_id);        
        
        // ToDo: on récupèrera l'entité Questionnaire et non l'id (conversion du paramètre via @ParamConverter)        
        $questionnaire_id = is_numeric($request->request->get('questionnaire')) ? $request->request->get('questionnaire') : 0;
        $questionnaire_id = 1; // pour test        
        if ($questionnaire_id == 0) {
            throw $this->createNotFoundException("Le questionnaire n'existe pas.");
        }

        $questionnaire = $em
                ->getRepository('FIANETSceauBundle:Questionnaire')
                ->find($questionnaire_id);
        
        // On vérifie que le questionnaire est bien actif
        // ToDo : renvoyer sur une page avec un message explicite pour le marchand
        if ($questionnaire->getActif() !== true) {
            throw $this->createNotFoundException("Le questionnaire n'existe pas ou plus.");
        }
        
        // On vérifie qu'on a le bon QuestionnaireReponse lié au questionnaire
        if (!$questionnaire->getQuestionnaireReponses()->contains($questionnaireReponse)) {
            throw $this->createNotFoundException("Pas de correspondance entre le commentaire et le questionnaire.");
        }
        
        // On vérifie s'il existe déjà un droit de réponse actif pour le commentaire
        // ToDo : revoir la méthode
        $nb_DroitDeReponse_Actif = $this->getDoctrine()->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                ->nbDroitDeReponseActif($questionnaireReponse);
        if ($nb_DroitDeReponse_Actif > 0) {
            throw $this->createNotFoundException("Un droit de réponse existe déjà pour ce commentaire.");
        }
        
        // On récupère les informations sur la commande, le membre, le site, le questionnaire répondu
        $infosGeneralesQuestionnaire = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->infosGeneralesQuestionnaire($questionnaire, $questionnaireType);
        
        // ToDo : on doit récupérer les informations de satisfaction pour le picto (vert, rouge, jaune, gris) et les transmettre au template
        
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


