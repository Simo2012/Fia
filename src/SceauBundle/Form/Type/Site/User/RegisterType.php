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
            'label'    => 'Nom :*',
            'required' => true,
            'attr'     => array(
                'size'         => '40',
                'caption'      => 'nom',
                'autocomplete' => false,

            )
        );
        $poBuilder->add('nom', 'text', $laParams);
        
        $laParams = array(
            'label'    => 'Prenom :*',
            'required' => true,
            'attr'     => array(
                'size'         => '40',
                'caption'      => 'prenom',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('prenom', 'text', $laParams);
        
        $laParams = array(
            'type' => 'email',
            'invalid_message' => 'Les Adresses Emails doit étres pareils',
            'options' => array('attr' => array('class' => 'email-field', 'size' => '40')),
            'required' => true,
            'first_options'  => array('label' => 'E-mail* :'),
            'second_options' => array('label' => 'E-mail de confirmation* :'),
            'mapped'      => false,
        );
        $poBuilder->add('email','repeated',$laParams);
        
        $laParams = array(
            'label'    => 'Pseudonyme :*',
            'required' => true,
            'attr'     => array(
                'caption'      => 'Pseudo',
                'autocomplete' => false,
                'size' => '40',
            )
        );
        $poBuilder->add('pseudo', 'text', $laParams);
        
        
        
        $laParams = array(
            'label'    => 'Password :*',
            'required' => true,
            'attr'     => array(
                'caption'      => 'Password',
                'autocomplete' => false,
                'class'        => 'password',
                'size' => '40',
            )
        );
        $poBuilder->add('password', 'password', $laParams);
        
        $laParams = array(
            'label' => 'Captcha :',
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

