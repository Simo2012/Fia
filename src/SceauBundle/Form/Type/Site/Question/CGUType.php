<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer une adresse e-mail valide.']),
                ],
            ])
            ->add('cgu', 'checkbox', [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez accepter les CGU pour valider votre questionnaire.']),
                ],
            ])
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