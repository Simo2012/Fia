<?php

namespace SceauBundle\Form\Type\Site\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            'type' => 'email',
            'invalid_message' => 'Les Adresses Emails doit étres pareils',
            'options' => array('attr' => array('class' => 'email-field')),
            'required' => true,
            'first_options'  => array('label' => 'E-mail* :'),
            'second_options' => array('label' => 'E-mail de confirmation* :'),
            'mapped'      => false,
        );
        $poBuilder->add('email','repeated',$laParams);
        
        $laParams = array(
            'label'    => 'Pseudonyme :',
            'required' => true,
            'attr'     => array(
                'caption'      => 'pseudo',
                'autocomplete' => false,
                'placeholder'  => 'pseudo'
            )
        );
        $poBuilder->add('pseudo', 'text', $laParams);
        
        
        
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
        
        $laParams = array(
            'attr'        => array(
                'options' => array(
                    'theme' => 'clean',
                    'type'  => 'image'
                )
            ),
            'mapped'      => false,
            'constraints' => array(
            )
        );
        $poBuilder->add('recaptcha', 'ewz_recaptcha', $laParams);
    } // buildForm

    
    /**
     * 
     * @param OptionsResolver $poResolver
     */
    public function configureOptions(OptionsResolver $poResolver)
    {
        $poResolver->setDefaults(
            array(
                'data_class'        => 'SceauBundle\Entity\Membre',
                'validation_groups' => array('registration'),
            )
        );
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'site_member_register';
    } // getName
}

