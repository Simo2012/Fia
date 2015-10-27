<?php

namespace SceauBundle\Controller\Site;

use SceauBundle\Entity\QuestionnaireReponse;
use SceauBundle\Form\Type\Site\QuestionnaireReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SurveyController
 * @package SceauBundle\Controller\Site
 *
 * @Route("/questionnaire")
 */
class SurveyController extends Controller
{
    /**
     * @Route("/{siteId}/{surveyId}", name="survey_index")
     * @param Request $request
     * @param integer $siteId
     * @param integer $surveyId
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function indexAction(Request $request, $siteId, $surveyId)
    {
        /** @var \SceauBundle\Entity\Repository\QuestionnaireRepository $questionnaireRepository */
        $questionnaireRepository = $this->getDoctrine()->getManager()->getRepository('SceauBundle:Questionnaire');

        /** @var \SceauBundle\Entity\Questionnaire $survey */
        if (!($survey = $questionnaireRepository->existsAndIsLinked($surveyId, $siteId))) {
            throw new \Exception($this->get('translator')->trans('questionnaire_invalide', [], 'erreurs'));
        }

        $questions = $survey->getQuestionnaireType()->getQuestions();

        $form = $this->createForm(new QuestionnaireReponseType(), null, [
            'questions' => $questions,
        ]);

        return $this->render('SceauBundle:Site\Survey:index.html.twig', [
            'survey' => $survey,
            'form'   => $form->createView(),
        ]);
    }
}