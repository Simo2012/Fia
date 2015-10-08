<?php

namespace SceauBundle\Form\Type\Site\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

/**
 * 
 */
class RegisterType extends AbstractType
{
    /**
    * @Recaptcha\IsTrue
    */
    public $recaptcha;
    
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $poBuilder, array $paOptions)
    {
        $laParams = array(
            'attr'     => array(
                'caption'   => 'Civilite :*',
            ),
            'choices' => array(
                'Mr' => 'Mr',
                'Mrs' => 'Mrs',
                'Miss' => 'Miss'
            ),
            'expanded' => true,
        );
        $poBuilder->add('civilite', 'choice', $laParams);
        
        $laParams = array(
            'attr'        => array(
                'options' => array(
                    'theme' => 'clean',
                    'type'  => 'image'
                )
            ),
            'mapped'      => false,
            'constraints' => array(
                new RecaptchaTrue()
            )
        );
        $poBuilder->add('recaptcha', 'ewz_recaptcha', $laParams);
       
        $laParams = array(
            'label'    => 'nom',
            'required' => true,
            'attr'     => array(
                'caption'      => 'nom',
                'autocomplete' => false,
                'placeholder'  => 'nom'
            )
        );
        $poBuilder->add('nom', 'text', $laParams);
        
        $laParams = array(
            'label'    => 'prenom',
            'required' => true,
            'attr'     => array(
                'caption'      => 'prenom',
                'autocomplete' => false,
                'placeholder'  => 'prenom'
            )
        );
        $poBuilder->add('prenom', 'text', $laParams);
        
        
        
        
        $laParams = array(
            'label'    => 'password',
            'required' => true,
            'attr'     => array(
                'caption'      => 'password',
                'autocomplete' => false,
                'placeholder'  => 'password',
                'class'        => 'password'
            )
        );
        $poBuilder->add('password', 'password', $laParams);
    } // buildForm

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $poResolver)
    {
        $poResolver->setDefaults(
            array(
                'data_class'        => 'SceauBundle\Entity\Membre',
                'validation_groups' => 'register'
            )
        );
    } // setDefaultOptions

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'site_member_register';
    } // getName
}

