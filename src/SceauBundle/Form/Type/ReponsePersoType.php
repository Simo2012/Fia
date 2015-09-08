<?php

namespace SceauBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponsePersoType extends ReponseType
{
    public function __construct($questionType_id)
    {
        parent::__construct($questionType_id);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('libelleCourt')
            ->remove('ordre')
            ->remove('actif');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SceauBundle\Entity\Reponse',
            'validation_groups' => 'perso'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sceaubundle_reponse_perso';
    }
}
