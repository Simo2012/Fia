<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtoileCommentaireType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \SceauBundle\Entity\Reponse[] $responses */
        $responses = $options['responses'];
        $min       = $options['min'];
        $max       = $options['max'];

        foreach ($responses as $response) {
            $builder->add($response->getId(), 'hidden', [
                'attr'     => [
                    'data-min' => $min,
                    'data-max' => $max,
                ],
                'label'    => 'Votre note :',
                'required' => false,
                'mapped'   => false,
            ]);
        }

        $builder->add('commentaire', 'textarea', [
            'label' => 'Votre commentaire',
            'attr'  => [
                'rows'      => 10,
                'maxlength' => 500,
            ],
            'required' => false,
            'mapped'   => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'responses' => [],
            'min'       => 0.5,
            'max'       => 5,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_question_etoile_commentaire';
    }
}