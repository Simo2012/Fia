<?php

namespace SceauBundle\Form\Type\Site\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 */
class PreferenceType extends AbstractType
{

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $poBuilder, array $paOptions)
    {
        
        //dump($paOptions['attr']);

    } // buildForm

    /**
     *
     * @param OptionsResolver $poResolver
     */
    public function configureOptions(OptionsResolver $poResolver)
    {
        $poResolver->setDefaults(
            array(
                'data_class' => 'SceauBundle\Entity\Membre',
            )
        );
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'site_membre_add_preference';
    } // getName
}
