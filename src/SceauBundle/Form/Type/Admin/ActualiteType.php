<?php

namespace SceauBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActualiteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Titre'
            ))
            ->add('introduction')
            ->add('content', 'textarea', array(
                'label' => 'Contenu'
            ))
            ->add('date', 'date', array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'label' => 'Date de publication',
                'attr' => array('class' => 'datepicker')
            ))
            ->add('active')
            ->add('save', 'submit', array(
                'attr' => array('class' => 'submit'),
                'label' => "Enregistrer",
                'attr' => array('class' => 'btn btn-info')
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SceauBundle\Entity\Actualite'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sceaubundle_actualite';
    }
}
