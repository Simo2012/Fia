<?php

namespace FIANET\SceauBundle\Form\Type\Extranet;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Date;

class QuestionnairesListeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$listeSites = array();
        foreach ($options['sites'] as $site) {
            $listeSites[$site->getId()] = $site->getNom();
        }*/

        $builder
            ->add(
                'dateDebut',
                'date',
                array(
                    'widget' => 'single_text',
                    'input' => 'string',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'constraints' => new Date(array('message' => 'La date n\'est pas valide.'))
                )
            )
            ->add(
                'dateFin',
                'date',
                array(
                    'widget' => 'single_text',
                    'input' => 'string',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'constraints' => new Date(array('message' => 'La date n\'est pas valide.'))
                )
            )
            ->add(
                'indicateur',
                'choice',
                array(
                    'choices' => array('vert' => 'V', 'jaune' => 'J', 'rouge' => 'R', 'gris' => 'G'),
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
                    'empty_value' => 'Tous',
                    'required' => false,
                )
            )
            ->add(
                'recherche',
                'text',
                array(
                    'required' => false,
                    'data' => 'Email, pseudonyme, nom, mot clé, numéro de commande'
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
            /*->add(
                'sites',
                'choice',
                array(
                    'choices' => $listeSites,
                    'empty_value' => 'Tous',
                    'multiple'  => false,
                    'required' => false
                )
            )*/
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

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'sites' => null
        ));
    }
}
