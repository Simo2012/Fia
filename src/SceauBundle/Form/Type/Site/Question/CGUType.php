<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CGUType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'attr' => [
                    'size'  => 30,
                    'style' => 'margin-top:4px;',
                ],
            ])
            ->add('cgu', 'checkbox')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_question_cgu';
    }
}