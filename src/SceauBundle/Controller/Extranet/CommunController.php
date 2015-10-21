<?php

namespace SceauBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommunController extends Controller
{
    /**
     * Met à jour la session avec le site sélectionné par l'utilisateur.
     * Un test est effectué pour vérifier que l'utilisateur a bien accès au site.
     * Il faut également réinitialiser la liste des types de questionnaire.
     *
     * @Route("/site_selectionne/{id}", name="extranet_commun_site_selectionne", options={"expose"=true})
     * @Method("GET")
     *
     * @param Request $request Instance de Request
     * @param integer $id Identifiant du site
     *
     * @return JsonResponse Retourne true si OK sinon false
     */
    public function selectionSiteAction(Request $request, $id)
    {
        $sitesUtilisateur = $this->getUser()->getSociete()->getSites();
        $numSite = 0;
        $siteTrouve = false;
        while (!$siteTrouve) {
            if ($sitesUtilisateur[$numSite]->getId() == $id) {
                $siteTrouve = true;
                $request->getSession()->set('siteSelectionne', $sitesUtilisateur[$numSite]);

                $request->getSession()->set(
                    'questionnaireTypeSelectionne',
                    $sitesUtilisateur[$numSite]->getQuestionnairePersonnalisations()[0]->getQuestionnaireType()
                );

            } else {
                $numSite++;
            }
        }

        if ($siteTrouve) {
            return new JsonResponse(true);
        } else {
            return new JsonResponse(false);
        }
    }

    /**
     * Met à jour la session avec le type de questionnaire sélectionné par l'utilisateur.
     * Un test est effectué pour vérifier que le type de questionnaire est bien lié au site sélectionné
     * par l'utilisateur.
     *
     * @Route("/questionaire_type_selectionne/{id}", name="extranet_commun_questionnaire_type_selectionne",
     *  options={"expose"=true})
     * @Method("GET")
     *
     * @param Request $request Instance de Request
     * @param integer $id Identifiant du type de questionnaire
     *
     * @return JsonResponse Retourne true si OK sinon false
     */
    public function selectionQuestionnaireTypeAction(Request $request, $id)
    {
        $questionnairePersonnalisations = $request->getSession()->get('siteSelectionne')
            ->getQuestionnairePersonnalisations();

        $numQuestionnaire = 0;
        $questionnaireTypeTrouve = false;
        while (!$questionnaireTypeTrouve) {
            if ($questionnairePersonnalisations[$numQuestionnaire]->getQuestionnaireType()->getId() == $id) {
                $questionnaireTypeTrouve = true;
                $request->getSession()->set(
                    'questionnaireTypeSelectionne',
                    $questionnairePersonnalisations[$numQuestionnaire]->getQuestionnaireType()
                );
            } else {
                $numQuestionnaire++;
            }
        }

        if ($questionnaireTypeTrouve) {
            return new JsonResponse(true);
        } else {
            return new JsonResponse(false);
        }
    }

    /**
     * Affiche une liste déroulante des types de questionnaires utilisés par un site.
     *
     * @param integer $id Identifiant du site
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeQuestionnaireTypeAction($id)
    {
        $siteSelectionne = null;
        foreach ($this->getUser()->getSociete()->getSites() as $site) {
            if ($site->getId() == $id) {
                $siteSelectionne = $site;
            }
        }

        $questionnaireTypes = array();
        foreach ($siteSelectionne->getQuestionnairePersonnalisations() as $questionnairePersonnalisation) {
            $questionnaireTypes[$questionnairePersonnalisation->getQuestionnaireType()->getId()]['libelle'] =
                $questionnairePersonnalisation->getQuestionnaireType()->getLibelle();
        }

        return $this->render(
            'SceauBundle:Extranet:liste_questionnaire_type.html.twig',
            array('questionnaireTypes' => $questionnaireTypes)
        );
    }

    /**
     * Get the last News from the database
     *
     */
    public function getLastNewsAction()
    {
        $actualiteRepo = $this->get('sceau.repository.actualite');
        $actualite = $actualiteRepo->findOneBy(
            array('active' => 'true'),
            array('date' => 'desc'),
            1,
            0
        );

        return $this->render('SceauBundle:Extranet/Actualite:actualite.html.twig', array('actualite' => $actualite));

    }


    /**
     * Get a news by id and an array of all news classified by month
     *
     * @Route("/ajax/{id}", name="extranet_commun_news")
     */

    public function getNewsAction($id)
    {
        $actualiteRepo = $this->get('sceau.repository.actualite');
        $actualite = $actualiteRepo->findOneById($id);

        $actualitesByMonths = $this->getNewsArchive();

       return $this->render('SceauBundle:Extranet/Actualite:actualite_content.html.twig', array(
           'actualite' => $actualite,
           'actualitesByMonths' => $actualitesByMonths,
       ));

    }

    /**
     * Get News's archives from the database classified by month.
     *
     * @return array contains all news classifed by month
     */
    private function getNewsArchive()
    {

        $actualiteRepo = $this->get('sceau.repository.actualite');

        $actualites = $actualiteRepo->findBy(
            array('active' => 'true'),
            array('date' => 'desc' )
        );

        $actualitesByMonths = array();

        //Group by month
        foreach ($actualites as $actualite){
            $actualiteDate = $actualite->getDate()->format('m-Y');
            if (isset($actualitesByMonths[$actualiteDate])) {
                $actualitesByMonths[$actualiteDate]['actualites'][] = $actualite;
            } else {
                $actualitesByMonths[$actualiteDate]['actualites'] = [$actualite];
                $actualitesByMonths[$actualiteDate]['date'] = $actualite->getDate();
                $actualitesByMonths[$actualiteDate]['id'] = $actualite->getId();
            }
        }

        return $actualitesByMonths;

    }
}
