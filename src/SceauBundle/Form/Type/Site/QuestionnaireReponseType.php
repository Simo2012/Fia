<?php

namespace SceauBundle\Form\Type\Site;

use SceauBundle\Entity\Question;
use SceauBundle\Entity\QuestionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionnaireReponseType extends AbstractType
{
    private $tombola         = false;
    private $livraison       = false;
    private $linkedQuestions = [];
    private $siteName        = null;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $questions */
        $questions = $options['questions'];

        /** @var \SceauBundle\Entity\Questionnaire $questionnaire */
        $questionnaire = $options['questionnaire'];

        if ($questionnaire) {
            $params          = $questionnaire->getQuestionnaireType()->getParametrage();
            $this->tombola   = isset($params['tombola']) ? $params['tombola'] : $this->tombola;
            $this->livraison = isset($params['livraison']) ? $params['livraison'] : $this->livraison;
            $this->siteName  = $questionnaire->getSite() ? $questionnaire->getSite()->getNom() : $this->siteName;
        }

        /** @var \SceauBundle\Entity\Question $question */
        foreach ($questions as $question) {
            if ($question->getCache()) {
                $visible = $question->getVisible();
                if (isset($visible['question_id']) && isset($visible['reponse_id'])) {
                    $questionId = $visible['question_id'];
                    $reponsesId = $visible['reponse_id'];

                    $this->linkedQuestions[$questionId][] = $question->getId();

                    if ($builder->has($questionId)) {
                        $builder->get($questionId)->addEventListener(
                            FormEvents::POST_SUBMIT,
                            function (FormEvent $event) use ($question, $reponsesId, $questionId) {
                                $form = $event->getForm();
                                if ($form->has('reponse')) {
                                    $data = $event->getForm()->get('reponse')->getData();
                                } else {
                                    $data = $form->getData();
                                }

                                if (in_array($data, $reponsesId)) {
                                    $this->addQuestion($event->getForm()->getParent(), $question, $questionId);
                                }
                            }
                        );
                    }
                }
                continue;
            }

            $this->addQuestion($builder, $question);
        }

        if ($questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant() && $this->livraison) {
            $builder->add('livraison', 'entity', [
                'class'        => 'SceauBundle\Entity\DelaiReception',
                'choice_label' => 'libelle',
                'expanded'     => false,
                'multiple'     => false,
                'label'        => 'Quand pensez-vous recevoir votre produit ? (Obligatoire)'
            ]);

            $builder->add('cgu', 'site_question_cgu');
        }

        $builder->add('optin', 'site_question_optin', [
            'label'    => 'J\'accepte de recevoir les offres des partenaires de FIA-NET adaptées à mon profil.',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'questions'     => [],
            'questionnaire' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'questionnaire_reponse';
    }

    /**
     * @param FormBuilderInterface|FormInterface $builder
     * @param Question $question
     * @param integer $parentQuestionId Id de la question décidant de l'affichage de la question courante
     */
    private function addQuestion($builder, Question $question, $parentQuestionId = null)
    {
        // Une réponse peut induire l'affichage de plusieurs questions.
        // Imaginons qu'une réponse à la question 9 entraine l'affichage des questions 10 et 11
        // Il faut que la question 11 soit affiché après la question 10 (et non pas la 9 qui est pourtant
        // son parent
        // On récupére donc l'id de la dernière question affichée après la question 9, ou la question 9 elle
        // même si aucun ajout n'a encore été fait.
        $after = $parentQuestionId;
        if ($parentQuestionId && isset($this->linkedQuestions[$parentQuestionId])) {
            $pos = array_search($question->getId(), $this->linkedQuestions[$parentQuestionId]);

            if ($pos) {
                $after = $this->linkedQuestions[$parentQuestionId][$pos - 1];
            }
        }
        $position = $after ? ['position' => ['after' => $after]] : [];

        // Si une réponse nécessite l'affichage d'un textarea pour précision, on récupére cette information ici.
        $precisions = $question->responsesNeedPrecision();

        switch ($question->getQuestionType()->getId()) {
            case QuestionType::CHOIX_UNIQUE:
                $builder->add($question->getId(), 'site_question_choice', [
                    'multiple'   => false,
                    'expanded'   => true,
                    'question'   => $question,
                    'label'      => $question->getLibelle($this->siteName),
                    'mapped'     => false,
                    'required'   => false,
                    'precisions' => $precisions,
                ] + $position);
                break;
            case QuestionType::CHOIX_UNIQUE_SELECT:
                $builder->add($question->getId(), 'choice', [
                    'choices'     => $question->getFormUsableResponse(),
                    'multiple'    => false,
                    'expanded'    => false,
                    'label'       => $question->getLibelle($this->siteName),
                    'mapped'      => false,
                    'required'    => false,
                    'empty_value' => false,
                ] + $position);
                break;
            case QuestionType::CHOIX_MULTIPLE:
                $builder->add($question->getId(), 'site_question_choice', [
                    'multiple'   => true,
                    'expanded'   => true,
                    'question'   => $question,
                    'label'      => $question->getLibelle($this->siteName),
                    'mapped'     => false,
                    'required'   => false,
                    'precisions' => $precisions,
                ] + $position);
                break;
            case QuestionType::NOTATION:
                $builder->add($question->getId(), 'site_question_notation', [
                    'responses' => $question->getReponses(),
                    'min'       => $question->getValeurMin(),
                    'max'       => $question->getValeurMax(),
                    'label'     => $question->getLibelle($this->siteName),
                    'mapped'    => false,
                    'required'  => false,
                ] + $position);
                break;
            case QuestionType::COMMENTAIRE:
                $builder->add($question->getId(), 'site_question_commentaire', [
                    'response'  => $question->getReponses()->first(),
                    'label'     => $question->getLibelle($this->siteName),
                    'mapped'    => false,
                    'required'  => false,
                    'tombola'   => $this->tombola,
                    'site_name' => $this->siteName,
                ] + $position);
                break;
            case QuestionType::ETOILE:
            case QuestionType::ETOILE_COMMENTAIRE:
                $builder->add($question->getId(), 'site_question_etoile_commentaire', [
                    'responses' => $question->getReponses(),
                    'min'       => $question->getValeurMin(),
                    'max'       => $question->getValeurMax(),
                    'label'     => $question->getLibelle($this->siteName),
                    'mapped'    => false,
                    'required'  => false,
                ] + $position);
                break;
        }
    }
}