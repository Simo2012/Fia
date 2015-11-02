<?php

namespace SceauBundle\Controller\Site;

use SceauBundle\Entity\Questionnaire;
use SceauBundle\Entity\QuestionnaireReponse;
use SceauBundle\Entity\QuestionType;
use SceauBundle\Form\Type\Site\QuestionnaireReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SurveyController
 * @package SceauBundle\Controller\Site
 *
 * @Route("/questionnaire")
 */
class QuestionnaireController extends Controller
{
    /**
     * @Route("/{siteId}/{questionnaireId}", name="questionnaire_index")
     *
     * @param Request $request
     * @param integer $siteId
     * @param integer $questionnaireId
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function indexAction(Request $request, $siteId, $questionnaireId)
    {
        /** @var \SceauBundle\Entity\Repository\QuestionnaireRepository $questionnaireRepository */
        $questionnaireRepository = $this->getDoctrine()->getManager()->getRepository('SceauBundle:Questionnaire');

        /** @var \SceauBundle\Entity\Questionnaire $questionnaire */
        if (!($questionnaire = $questionnaireRepository->existsAndIsLinked($questionnaireId, $siteId))) {
            throw new \Exception($this->get('translator')->trans('questionnaire_invalide', [], 'erreurs'));
        }

        /** @var \SceauBundle\Entity\Question[] $questions */
        $questions = $questionnaire->getQuestionnaireType()->getQuestions();

        $form = $this->createForm(new QuestionnaireReponseType(), null, [
            'questions'     => $questions,
            'questionnaire' => $questionnaire,
        ]);

        $form->handleRequest($request);
        $displayMore = $request->get('display_more', false);
        if ($form->isValid() && ! $displayMore) {
            $this->persistQuestionnaireReponses($form, $questionnaire);
        }

        $linkedQuestions = [];
        foreach ($questions as $question) {
            if ($question->getCache()) {
                $visible = $question->getVisible();
                if (isset($visible['question_id']) && isset($visible['reponse_id'])) {
                    if (!isset($linkedQuestions[$visible['question_id']])) {
                        $linkedQuestions[$visible['question_id']] = [];
                    }
                    $linkedQuestions[$visible['question_id']] += $visible['reponse_id'];
                }
            }
        }

        return $this->render('SceauBundle:Site\Questionnaire:index.html.twig', [
            'questionnaire'   => $questionnaire,
            'form'            => $form->createView(),
            'linkedQuestions' => json_encode($linkedQuestions),
        ]);
    }

    /**
     * @param Form $form
     * @param Questionnaire $questionnaire
     */
    private function persistQuestionnaireReponses(Form $form, Questionnaire $questionnaire)
    {
        foreach ($questionnaire->getQuestionnaireType()->getQuestions() as $question) {
            /** @var \SceauBundle\Entity\Reponse[] $reponses */
            $reponses = [];
            foreach ($question->getReponses() as $reponse) {
                $reponses[$reponse->getId()] = $reponse;
            }

            $commentaire = null;
            $reponsesId = [];
            $notes = [];
            if ($form->has($question->getId())) {
                switch ($question->getQuestionType()->getId()) {
                    case QuestionType::CHOIX_UNIQUE:
                        $reponsesId[] = $form->get($question->getId())->get('reponse')->getData();
                        break;
                    case QuestionType::CHOIX_UNIQUE_SELECT:
                        $reponsesId[] = $form->get($question->getId())->getData();
                        break;
                    case QuestionType::CHOIX_MULTIPLE:
                        $reponsesId = $form->get($question->getId())->get('reponse')->getData();
                        break;
                    case QuestionType::NOTATION:
                        foreach ($reponses as $reponse) {
                            if (($note = $form->get($question->getId())->get($reponse->getId())->getData())) {
                                $notes[$reponse->getId()] = $form
                                    ->get($question->getId())
                                    ->get($reponse->getId())
                                    ->getData();
                                $reponsesId[] = $reponse->getId();
                            }
                        }
                        break;
                    case QuestionType::COMMENTAIRE:
                        $commentaire = $form
                            ->get($question->getId())
                            ->get($question->getReponses()->first()->getId())
                            ->getData();
                        $reponsesId[] = $question->getReponses()->first()->getId();
                        break;
                    case QuestionType::ETOILE:
                    case QuestionType::ETOILE_COMMENTAIRE:
                }
            }

            foreach ($reponsesId as $reponseId) {
                if (isset($reponses[$reponseId])) {
                    $questionnaireReponse = new QuestionnaireReponse();
                    $questionnaireReponse->setQuestion($question);
                    $questionnaireReponse->setQuestionnaire($questionnaire);
                    $questionnaireReponse->setReponse($reponses[$reponseId]);
                    if ($reponses[$reponseId]->getPrecision()
                        && ($precision = $form->get($question->getId())->get($reponseId)->getData())) {
                        $questionnaireReponse->setCommentaire($precision);
                    }
                    if ($commentaire) {
                        $questionnaireReponse->setCommentaire($commentaire);
                    }
                    if (!empty($notes) && isset($notes[$reponseId])) {
                        $questionnaireReponse->setNote($notes[$reponseId]);
                    }
                    //$this->getDoctrine()->getManager()->persist($questionnaireReponse);
                }
            }
        }

        //$this->getDoctrine()->getManager()->flush();
    }
}