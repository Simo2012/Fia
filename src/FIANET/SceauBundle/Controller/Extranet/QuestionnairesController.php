<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use DateTime;
use FIANET\SceauBundle\Entity\DroitDeReponse;
use FIANET\SceauBundle\Exception\Extranet\AccesInterditException;
use FIANET\SceauBundle\Form\Type\Extranet\QuestionnairesListeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class QuestionnairesController extends Controller
{
    /**
     * Affiche le tableau de bord des questionnaires.
     *
     * @Route("/questionnaires", name="extranet_questionnaires")
     * @Method("GET")
     */
    public function indexAction()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.dashboard')->setCurrent(true);

        return $this->render('FIANETSceauBundle:Extranet/Questionnaires:index.html.twig');
    }

    /**
     * Affiche la page de listing des questionnaires. Si l'action est appelée via AJAX, elle retourne les questionnaires
     * par paquet de lignes de tableau (utilisé pour "l'infinite scroll").
     *
     * @Route("/questionnaires/questionnaires", name="extranet_questionnaires_questionnaires", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request Instance de Request
     *
     * @return Response
     */
    public function questionnairesAction(Request $request)
    {
        $nbQuestionnairesMax = $this->container->getParameter('nb_questionnaires_max');
        $site = $request->getSession()->get('siteSelectionne');

        if (!$request->isXmlHttpRequest()) {
            $menu = $this->get('fianet_sceau.extranet.menu');
            $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

            $form = $this->createForm(
                new QuestionnairesListeType(),
                null/*,
                array('sites' => array($this->getUser()->getSite()))*/
            );
            $form->handleRequest($request);

            $donneesForm = $form->getData();
            $tri = is_numeric($donneesForm['tri']) ? $donneesForm['tri'] : 2;
            $dateDebut = (isset($donneesForm['dateDebut'])) ? $donneesForm['dateDebut'] : '';
            $dateFin = (isset($donneesForm['dateFin'])) ? $donneesForm['dateFin'] : '';

            /* Formulaire non soumis (1er chargement de la page) ou formulaire soumis et valide */
            if (!$form->isSubmitted() || ($form->isSubmitted() && $form->isValid())) {
                $nbTotalQuestionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->nbTotalQuestionnaires(
                        $site,
                        $dateDebut,
                        $dateFin
                    );

                $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                    ->listeQuestionnaires(
                        $site,
                        $dateDebut,
                        $dateFin,
                        0,
                        $nbQuestionnairesMax,
                        $tri
                    );
            } else {
                /* Formulaire soumis et non valide */
                $nbTotalQuestionnaires = 0;
                $questionnaires = array();
            }

            return $this->render(
                'FIANETSceauBundle:Extranet/Questionnaires:questionnaires.html.twig',
                array(
                    'nbTotalQuestionnaires' => $nbTotalQuestionnaires,
                    'questionnaires' => $questionnaires,
                    'nbQuestionnairesMax' => $nbQuestionnairesMax,
                    'form' => $form->createView(),
                    'offset' => 0,
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin,
                    'tri' => $tri
                )
            );

        } else {
            $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                ->listeQuestionnaires(
                    $site,
                    $request->request->get('dateDebut'),
                    $request->request->get('dateFin'),
                    $request->request->get('offset', 0),
                    $this->container->getParameter('nb_questionnaires_max'),
                    $request->request->get('tri')
                );

            return $this->render(
                'FIANETSceauBundle:Extranet/Questionnaires:questionnaires_lignes.html.twig',
                array(
                    'questionnaires' => $questionnaires,
                    'offset' => $request->request->get('offset', 0)
                )
            );
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
                    ->infosGeneralesQuestionnaire($questionnaire);
        
        // ToDo : on doit récupérer les informations de satisfaction pour le picto (vert, rouge, jaune, gris) et les transmettre au template
        
        // Construction du formulaire pour le droit de réponse
        $droitDeReponse = new DroitDeReponse();
        $formBuilder = $this->get('form.factory')->createBuilder('form', $droitDeReponse);

        $formBuilder
                ->add('commentaire', 'textarea', array(
                    'trim' => true))
                ->add('valider', 'submit')      
        ; // 'allow_extra_fields' => true

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
                'questionnaireReponse' => $questionnaireReponse
            )
        );
    }

}

