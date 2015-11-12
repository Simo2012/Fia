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
        if ($form->isValid() && !$displayMore) {
            $this->persistQuestionnaireReponses($form, $questionnaire);

            if ($form->has('livraison')) {
                $delai = $form->get('livraison')->getData();
            }
            if ($form->has('cgu')) {
                $email = $form->get('cgu')->get('email')->getData();
                $cgu   = $form->get('cgu')->get('cgu')->getData();
            }
            if ($form->has('optin')) {
                $optin = $form->get('optin')->get('optin')->getData();

                if ($optin) {
                    $civility  = $form->get('optin')->get('civility')->getData();
                    $lastName  = $form->get('optin')->get('lastName')->getData();
                    $firstName = $form->get('optin')->get('firstName')->getData();
                }
            }
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
            'error'           => !$form->isValid() && $request->getMethod() === 'POST' && !$displayMore,
        ]);
    }

    /**
     * @param Form $form
     * @param Questionnaire $questionnaire
     */
    private function persistQuestionnaireReponses(Form $form, Questionnaire $questionnaire)
    {
        /** @var \SceauBundle\Entity\Question $question */
        foreach ($questionnaire->getQuestionnaireType()->getQuestions() as $question) {
            // If question have a parent one, we skip it since responses will be handle with parent question
            if ($question->getQuestionPrimaire()) {
                continue;
            }

            /** @var \SceauBundle\Entity\Reponse[] $responses */
            $responses = [];
            foreach ($question->getReponses() as $response) {
                $responses[$response->getId()] = $response;
            }

            $responsesId = [];
            $notes       = [];
            $comment     = null;
            if ($form->has($question->getId())) {
                switch ($question->getQuestionType()->getId()) {
                    case QuestionType::CHOIX_UNIQUE:
                    case QuestionType::CHOIX_UNIQUE_SELECT:
                    case QuestionType::CHOIX_UNIQUE_INLINE:
                        $responsesId[] = $form->get($question->getId())->get('reponse')->getData();
                        break;
                    case QuestionType::CHOIX_MULTIPLE:
                        $responsesId = $form->get($question->getId())->get('reponse')->getData();
                        break;
                    case QuestionType::NOTATION:
                        foreach ($responses as $response) {
                            if (($note = $form->get($question->getId())->get($response->getId())->getData())) {
                                $notes[$response->getId()] = $form
                                    ->get($question->getId())
                                    ->get($response->getId())
                                    ->getData();
                                $responsesId[] = $response->getId();
                            }
                        }
                        break;
                    case QuestionType::COMMENTAIRE:
                        $comment = $form
                            ->get($question->getId())
                            ->get($question->getReponses()->first()->getId())
                            ->getData();
                        $responsesId[] = $question->getReponses()->first()->getId();
                        break;
                    case QuestionType::ETOILE:
                    case QuestionType::ETOILE_COMMENTAIRE:
                        foreach ($responses as $response) {
                            if (($note = $form->get($question->getId())->get($response->getId())->getData())) {
                                $notes[$response->getId()] = $form
                                    ->get($question->getId())
                                    ->get($response->getId())
                                    ->getData();
                                $responsesId[] = $response->getId();
                            }
                        }

                        $comment = $form->get($question->getId())->get('commentaire')->getData();
                        break;
                    case QuestionType::QUESTION_MULTIPLE:
                        foreach ($question->getQuestionsSecondaires() as $questionSecondaire) {
                            $data = $form
                                ->get($question->getId())
                                ->get($questionSecondaire->getId())
                                ->get('reponse')
                                ->getData()
                            ;

                            if ($data) {
                                $response = $questionSecondaire->getReponses()->filter(function($reponse) use ($data) {
                                    return $reponse->getId() === $data;
                                })->first();
                                if ($response) {
                                    $questionnaireReponse = new QuestionnaireReponse();
                                    $questionnaireReponse->setQuestion($question);
                                    $questionnaireReponse->setQuestionnaire($questionnaire);
                                    $questionnaireReponse->setReponse($response);
                                    $this->getDoctrine()->getManager()->persist($questionnaireReponse);
                                }
                            }
                        }
                        break;
                }
            }

            foreach ($responsesId as $responseId) {
                if (isset($responses[$responseId])) {
                    $questionnaireReponse = new QuestionnaireReponse();
                    $questionnaireReponse->setQuestion($question);
                    $questionnaireReponse->setQuestionnaire($questionnaire);
                    $questionnaireReponse->setReponse($responses[$responseId]);
                    if ($responses[$responseId]->getPrecision()
                        && ($precision = $form->get($question->getId())->get($responseId)->getData())) {
                        $questionnaireReponse->setCommentaire($precision);
                    }
                    if ($comment) {
                        $questionnaireReponse->setCommentaire($comment);
                    }
                    if (!empty($notes) && isset($notes[$responseId])) {
                        $questionnaireReponse->setNote($notes[$responseId]);
                    }
                    $this->getDoctrine()->getManager()->persist($questionnaireReponse);
                }
            }
        }

        $this->getDoctrine()->getManager()->flush();
    }
}