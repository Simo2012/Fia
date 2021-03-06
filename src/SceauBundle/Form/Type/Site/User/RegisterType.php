<?php

namespace SceauBundle\Form\Type\Site\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegisterType extends AbstractType
{
    /**
     * @Recaptcha\IsTrue
     */
    public $recaptcha;

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $poBuilder, array $paOptions)
    {
        $laParams = array(
            'attr' => array(
                'caption' => 'Civilite :*',

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
            'label' => 'Nom :*',
            'required' => true,
            'attr' => array(
                'size' => '40',
                'caption' => 'nom',
                'autocomplete' => false,

            )
        );
        $poBuilder->add('nom', 'text', $laParams);


        $laParams = [
            'label' => 'Prenom :*',
            'required' => true,
            'attr' => ['size' => '40', 'caption' => 'prenom', 'autocomplete' => false]
        ];
        $poBuilder->add('prenom', 'text', $laParams);

        $laParams = array(
            'type' => 'email',
            'invalid_message' => 'Les Adresses Emails doit étres pareils',
            'options' => array('attr' => array('class' => 'email-field', 'size' => '40')),
            'required' => true,
            'first_options' => array('label' => 'E-mail* :'),
            'second_options' => array('label' => 'E-mail de confirmation* :'),
            'mapped' => false,
        );
        $poBuilder->add('email', 'repeated', $laParams);

        $laParams = array(
            'label' => 'Pseudonyme :*',
            'required' => true,
            'attr' => array(
                'caption' => 'Pseudo',
                'autocomplete' => false,
                'size' => '40',
            )
        );

        $poBuilder->add('pseudo', 'text', $laParams);
        if (isset($paOptions['attr']['tombola'])) {
            $laParams = array(
                'label' => 'Pays :*',
                'class' => 'SceauBundle:Pays',
                'property' => 'libelle',
                'required' => true,
                'attr' => array(
                    'caption' => 'pays',
                    'autocomplete' => false,
                ),
                'mapped' => false,
            );
            $poBuilder->add('pays', 'entity', $laParams);

            $laParams = array(
                'label' => 'Adresse :',
                'attr' => array(
                    'required' => true,
                    'caption' => 'adresse',
                    'autocomplete' => false,
                    'size' => '40',
                ),
                'required' => false,
                'mapped' => false,
            );
            $poBuilder->add('adresse', 'text', $laParams);

            $laParams = array(
                'label' => 'Complément d\'adresse :',
                'attr' => array(
                    'caption' => 'compAdresse',
                    'autocomplete' => false,
                    'size' => '40',
                ),
                'required' => false,
                'mapped' => false,
            );
            $poBuilder->add('compAdresse', 'text', $laParams);

            $laParams = array(
                'label' => 'Code Postal :',
                'attr' => array(
                    'caption' => 'codePostal',
                    'autocomplete' => false,
                    'size' => '10',
                ),
                'mapped' => false,
                'required' => false,
            );
            $poBuilder->add('codePostal', 'text', $laParams);

            $laParams = array(
                'label' => 'Ville :',
                'attr' => array(
                    'caption' => 'ville',
                    'autocomplete' => false,
                    'size' => '40',
                ),
                'mapped' => false,
                'required' => false,
            );
            $poBuilder->add('ville', 'text', $laParams);
        }


        $laParams = array(
            'label' => 'Password :*',
            'required' => true,
            'attr' => array(
                'caption' => 'Password',
                'autocomplete' => false,
                'class' => 'password',
                'size' => '40',
            )
        );
        $poBuilder->add('password', 'password', $laParams);

        $laParams = array(
            'label' => 'Captcha :',
            'attr' => array(
                'options' => array(
                    'theme' => 'clean',
                    'type' => 'image'
                )
            ),
            'mapped' => false,
            'error_bubbling' => true,
            'constraints' => array(
                new RecaptchaTrue()
            ),
        );
        $poBuilder->add('recaptcha', 'ewz_recaptcha', $laParams);

        $poBuilder->add('legal', 'checkbox', array(
            'mapped' => false,
            'empty_data' => false,
            'required' => 'required',
            'label' => 'J\'accepte de recevoir les offres des partenaires de FIA-NET adaptées à mon profil.',
            'data' => false,
            'constraints' => new IsTrue(
                array(
                    'message' => 'J\'accepte de recevoir les offres des partenaires de FIA-NET adaptées à mon profil.',
                    'groups' => 'registration')
            )));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $poResolver)
    {
        $poResolver->setDefaults(
            array(
                'data_class' => 'SceauBundle\Entity\Membre',
                'validation_groups' => array('registration')
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'site_member_register';
    }
}
