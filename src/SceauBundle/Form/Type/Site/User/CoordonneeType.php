<?php

namespace SceauBundle\Form\Type\Site\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 */
class CoordonneeType extends AbstractType
{

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $poBuilder, array $paOptions)
    {
        $laParams = array(
            'label' => 'Pays :*',
            'class' => 'SceauBundle:Pays',
            'property' => 'libelle',
        );
        $poBuilder->add('pays', 'entity', $laParams);

        $laParams = array(
            'label' => 'Adresse :',
            'required' => false,
            'attr' => array(
                'size' => '30',
                'caption' => 'adresse',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('adresse', 'text', $laParams);

        $laParams = array(
            'label' => 'Complément d\'adresse :',
            'required' => false,
            'attr' => array(
                'size' => '30',
                'caption' => 'adresse',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('compAdresse', 'text', $laParams);

        $laParams = array(
            'label' => 'Code postal :',
            'required' => false,
            'attr' => array(
                'size' => '15',
                'caption' => 'codePostal',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('codePostal', 'text', $laParams);

        $laParams = array(
            'label' => 'Ville :',
            'required' => false,
            'attr' => array(
                'size' => '30',
                'caption' => 'codePostal',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('ville', 'text', $laParams);

        $laParams = array(
            'label' => 'Téléphone fixe :',
            'required' => false,
            'attr' => array(
                'size' => '30',
                'caption' => 'telephoneFixe',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('telephoneFixe', 'text', $laParams);

        $laParams = array(
            'label' => 'Téléphone mobile :',
            'required' => false,
            'attr' => array(
                'size' => '30',
                'caption' => 'telephoneMobile',
                'autocomplete' => false,
            )
        );
        $poBuilder->add('telephoneMobile', 'text', $laParams);

        //codePostal


    } // buildForm

    /**
     *
     * @param OptionsResolver $poResolver
     */
    public function configureOptions(OptionsResolver $poResolver)
    {
        $poResolver->setDefaults(
            array(
                'data_class' => 'SceauBundle\Entity\Coordonnee',
            )
        );
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'sceauSiteUserFormCoordonnees';
    } // getName
}
