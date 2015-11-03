<?php

namespace SceauBundle\Form\Type\Site\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 */
class UpdateType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $poBuilder, array $paOptions)
    {
        //var_dump($paOptions['attr']['locationId']);
        $laParams = array(
            'class' => 'SceauBundle:Avatar',
            'property' => 'number',
        );
        $poBuilder->add('avatar', 'entity', $laParams);

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
            'required' => true,
        );
        $poBuilder->add('civilite', 'choice', $laParams);


        $laParams = array(
            'label' => 'Nom :*',
            'required' => true,
            'attr' => array(
                'size' => '30',
                'caption' => 'nom',
                'autocomplete' => false,

            )
        );
        $poBuilder->add('nom', 'text', $laParams);


        $laParams = array(
            'label' => 'Prenom :*',
            'required' => true,
            'attr' => array(
                'size' => '30',
                'caption' => 'prenom',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('prenom', 'text', $laParams);

        $laParams = array(
            'label' => 'Activité professionnelle :',
            'class' => 'SceauBundle:ActiviteProfessionnelle',
            'property' => 'activite',
        );
        $poBuilder->add('activiteprofessionnelle', 'entity', $laParams);

        $laParams = array(
            'label' => 'Situation familiale :',
            'class' => 'SceauBundle:SituationFamiliale',
            'property' => 'situation',
        );
        $poBuilder->add('situationfamiliale', 'entity', $laParams);

        $laParams = array(
            'label' => 'Age :',
            'class' => 'SceauBundle:TrancheAge',
            'property' => 'libelle',
        );
        $poBuilder->add('trancheAge', 'entity', $laParams);


        $laParams = array(
            'label' => 'Pseudonyme :*',
            'required' => true,
            'attr' => array(
                'caption' => 'Pseudo',
                'autocomplete' => false,
                'size' => '30',
            )
        );
        $poBuilder->add('pseudo', 'text', $laParams);


        $poBuilder->add('coordonnee', new CoordonneeType());

    } // buildForm

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
        return 'site_member_update';
    }
}
