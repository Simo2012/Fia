<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
            'FIANETSceauBundle:Extranet:liste_questionnaire_type.html.twig',
            array('questionnaireTypes' => $questionnaireTypes)
        );
    }
}
