<?php

namespace SceauBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticlePresseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('date')
            ->add('content', null, array(
                'attr' => array('class' => 'form-control', 'id' => 'datepickerId')
            ))
            ->add('source', null, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('urlSource', null, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('published', null, array(
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SceauBundle\Entity\ArticlePresse'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sceaubundle_articlepresse';
    }
}
