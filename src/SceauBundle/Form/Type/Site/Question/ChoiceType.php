<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \SceauBundle\Entity\Question $question */
        $question   = $options['question'];
        $precisions = $options['precisions'];
        $multiple   = $options['multiple'];
        $expanded   = $options['expanded'];

        if ($question) {
            $builder->add('reponse', 'choice', [
                'choices'     => $question->getFormUsableResponse(),
                'multiple'    => $multiple,
                'expanded'    => $expanded,
                'mapped'      => false,
                'required'    => false,
                'empty_value' => false,
            ]);

            foreach ($precisions as $precision) {
                $builder->add($precision, 'textarea', [
                    'required' => false,
                    'mapped'   => false,
                    'attr'     => [
                        'maxlength' => 500,
                    ],
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
            'question'   => null,
            'precisions' => [],
            'multiple'   => false,
            'expanded'   => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_question_choice';
    }
}