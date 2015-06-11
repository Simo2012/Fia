<?php

namespace FIANET\SceauBundle\Form\Type\Extranet;

use IntlDateFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Date;

class QuestionnairesListeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'dateDebut',
                'date',
                array(
                    'widget' => 'single_text',
                    'input' => 'string',
                    'format' => IntlDateFormatter::SHORT,
                    'required' => false,
                    'constraints' => new Date(array('message' => 'constraints.date_invalide'))
                )
            )
            ->add(
                'dateFin',
                'date',
                array(
                    'widget' => 'single_text',
                    'input' => 'string',
                    'format' => IntlDateFormatter::SHORT,
                    'required' => false,
                    'constraints' => new Date(array('message' => 'constraints.date_invalide'))
                )
            )
            ->add(
                'indicateurs',
                'choice',
                array(
                    'choices' => array('vert' => ' ', 'jaune' => ' ', 'rouge' => ' ', 'gris' => ' '),
                    'multiple' => true,
                    'expanded' => true
                )
            )
            ->add(
                'livraison',
                'entity',
                array(
                    'class' => 'FIANETSceauBundle:LivraisonType',
                    'property' => 'libelle',
                    'empty_value' => 'livraisons_tous',
                    'required' => false,
                    'translation_domain' => 'livraisons'
                )
            )
            ->add(
                'recherche',
                'text',
                array(
                    'required' => false
                )
            )
            ->add(
                'litige',
                'checkbox',
                array('required' => false)
            )
            ->add(
                'retenir',
                'checkbox',
                array('required' => false)
            )
            ->add(
                'tri',
                'hidden',
                array('data' => 2)
            )
            ->setMethod('POST');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'questionnaires_liste';
    }
}
