<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use FIANET\SceauBundle\Exception\Extranet\AccesInterditException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        if (!$request->isXmlHttpRequest()) {
            $menu = $this->get('fianet_sceau.extranet.menu');
            $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

            $nbTotalQuestionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                ->getNbTotalQuestionnaires(
                    $this->getUser()->getSite()
                );

            $tri = is_numeric($request->request->get('tri')) ? $request->request->get('tri') : 2;

            $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                ->getListeQuestionnaires(
                    $this->getUser()->getSite(),
                    0,
                    $nbQuestionnairesMax,
                    $tri
                );

            return $this->render(
                'FIANETSceauBundle:Extranet/Questionnaires:questionnaires.html.twig',
                array(
                    'nbTotalQuestionnaires' => $nbTotalQuestionnaires,
                    'questionnaires' => $questionnaires,
                    'nbQuestionnairesMax' => $nbQuestionnairesMax,
                    'alternatif' => false,
                    'tri' => $tri
                )
            );

        } else {
            $questionnaires = $this->getDoctrine()->getRepository('FIANETSceauBundle:Questionnaire')
                ->getListeQuestionnaires(
                    $this->getUser()->getSite(),
                    $request->request->get('offset', 0),
                    $this->container->getParameter('nb_questionnaires_max'),
                    $request->request->get('tri')
                );

            return $this->render(
                'FIANETSceauBundle:Extranet/Questionnaires:questionnaires_lignes.html.twig',
                array(
                    'questionnaires' => $questionnaires,
                    'alternatif' => true
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
}

