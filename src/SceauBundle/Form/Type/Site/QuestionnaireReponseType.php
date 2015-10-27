<?php

namespace SceauBundle\Form\Type\Site;

use SceauBundle\Entity\QuestionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionnaireReponseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \SceauBundle\Entity\Question[] $questions */
        $questions = $options['questions'];

        foreach ($questions as $question) {
            switch ($question->getQuestionType()->getId()) {
                case QuestionType::CHOIX_UNIQUE:
                    $builder->add($question->getId(), 'choice', [
                        'choices'  => $question->getFormUsableResponse(),
                        'multiple' => false,
                        'expanded' => true,
                        'label'    => $question->getLibelle(),
                        'mapped'   => false,
                    ]);
                    break;
                case QuestionType::CHOIX_UNIQUE_SELECT:
                    $builder->add($question->getId(), 'choice', [
                        'choices'  => $question->getFormUsableResponse(),
                        'multiple' => false,
                        'expanded' => false,
                        'label'    => $question->getLibelle(),
                        'mapped'   => false,
                    ]);
                    break;
                case QuestionType::CHOIX_MULTIPLE:
                    $builder->add($question->getId(), 'choice', [
                        'choices'  => $question->getFormUsableResponse(),
                        'multiple' => true,
                        'expanded' => true,
                        'label'    => $question->getLibelle(),
                        'mapped'   => false,
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
                        ]);
                    }
                    break;
                case QuestionType::COMMENTAIRE:
                    $builder->add($question->getId(), 'commentaire', [
                        'required' => false,
                        'mapped'   => false,
                        'response' => $question->getReponses()->first(),
                        'label'    => $question->getLibelle(),
                    ]);
                    break;
                case QuestionType::ETOILE:
                case QuestionType::ETOILE_COMMENTAIRE:
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'questions'  => [],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'questionnaire_reponse';
    }
}