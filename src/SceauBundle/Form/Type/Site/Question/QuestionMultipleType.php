<?php

namespace SceauBundle\Form\Type\Site\Question;

use SceauBundle\Entity\QuestionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionMultipleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $questions */
        $questions = $options['questions'];

        /** @var \SceauBundle\Entity\Questionnaire $questionnaire */
        $questionnaire = $options['questionnaire'];

        $siteName = ($questionnaire && $questionnaire->getSite()) ? $questionnaire->getSite()->getNom() : null;

        /** @var \SceauBundle\Entity\Question $question */
        foreach ($questions as $question) {
            if ($question->getQuestionType()->getId() === QuestionType::CHOIX_UNIQUE) {
                $builder->add($question->getId(), 'site_question_choice', [
                    'multiple'   => false,
                    'expanded'   => true,
                    'question'   => $question,
                    'label'      => $question->getLibelle($siteName),
                    'mapped'     => false,
                    'required'   => false,
                ]);
            }
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
        return 'site_question_multiple';
    }
}