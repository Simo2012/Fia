<?php

namespace FIANET\SceauBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReponsePersoType extends ReponseType
{
    public function __construct($questionType_id)
    {
        parent::__construct($questionType_id);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Reponse',
            'validation_groups' => 'perso'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fianet_sceaubundle_reponse_perso';
    }
}
