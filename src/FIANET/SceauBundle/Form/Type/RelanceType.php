<?php

namespace FIANET\SceauBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RelanceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objet', 'text', array('required' => false))
            ->add('corps', 'textarea', array('required' => false));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Relance'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fianet_sceaubundle_relance';
    }
}
