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
    private $tombola   = false;
    private $livraison = false;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $questions */
        $questions     = $options['questions'];

        /** @var \SceauBundle\Entity\Questionnaire $questionnaire */
        $questionnaire = $options['questionnaire'];

        if ($questionnaire) {
            $params          = $questionnaire->getQuestionnaireType()->getParametrage();
            $this->tombola   = isset($params['tombola']) ? $params['tombola'] : $this->tombola;
            $this->livraison = isset($params['livraison']) ? $params['livraison'] : $this->livraison;
        }

        /** @var \SceauBundle\Entity\Question $question */
        foreach ($questions as $question) {
            if ($question->getCache()) {
                $visible = $question->getVisible();
                if (isset($visible['question_id']) && isset($visible['reponse_id'])) {
                    $questionId = $visible['question_id'];
                    $reponsesId = $visible['reponse_id'];

                    if ($builder->has($questionId)) {
                        $builder->get($questionId)->addEventListener(
                            FormEvents::POST_SUBMIT,
                            function (FormEvent $event) use ($question, $reponsesId) {
                                $data = $event->getForm()->getData();

                                if (in_array($data, $reponsesId)) {
                                    $this->addQuestion($event->getForm()->getParent(), $question);
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
        }


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
     */
    private function addQuestion($builder, Question $question)
    {
        switch ($question->getQuestionType()->getId()) {
            case QuestionType::CHOIX_UNIQUE:
                $builder->add($question->getId(), 'choice', [
                    'choices'  => $question->getFormUsableResponse(),
                    'multiple' => false,
                    'expanded' => true,
                    'label'    => $question->getLibelle(),
                    'mapped'   => false,
                    'required' => false,
                    'empty_value' => false,
                ]);
                break;
            case QuestionType::CHOIX_UNIQUE_SELECT:
                $builder->add($question->getId(), 'choice', [
                    'choices'  => $question->getFormUsableResponse(),
                    'multiple' => false,
                    'expanded' => false,
                    'label'    => $question->getLibelle(),
                    'mapped'   => false,
                    'required' => false,
                    'empty_value' => false,
                ]);
                break;
            case QuestionType::CHOIX_MULTIPLE:
                $builder->add($question->getId(), 'choice', [
                    'choices'  => $question->getFormUsableResponse(),
                    'multiple' => true,
                    'expanded' => true,
                    'label'    => $question->getLibelle(),
                    'mapped'   => false,
                    'required' => false,
                ]);
                break;
            case QuestionType::NOTATION:
                if (($min = $question->getValeurMin()) && ($max = $question->getValeurMax())) {
                    $builder->add($question->getId(), 'notation', [
                        'responses' => $question->getReponses(),
                        'min'       => $question->getValeurMin(),
                        'max'       => $question->getValeurMax(),
                        'label'     => $question->getLibelle(),
                        'mapped'    => false,
                        'required' => false,
                    ]);
                }
                break;
            case QuestionType::COMMENTAIRE:
                $builder->add($question->getId(), 'commentaire', [
                    'required' => false,
                    'mapped'   => false,
                    'response' => $question->getReponses()->first(),
                    'label'    => $question->getLibelle(),
                    'tombola'  => $this->tombola,
                ]);
                break;
            case QuestionType::ETOILE:
            case QuestionType::ETOILE_COMMENTAIRE:
        }
    }
}