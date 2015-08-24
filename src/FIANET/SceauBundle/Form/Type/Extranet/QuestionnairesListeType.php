<?php

namespace FIANET\SceauBundle\Form\Type\Extranet;

use FIANET\SceauBundle\Entity\LivraisonTypeRepository;
use FIANET\SceauBundle\Service\OutilsString;
use IntlDateFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Date;

class QuestionnairesListeType extends AbstractType
{
    protected $outilsString;

    public function __construct(OutilsString $outilsString)
    {
        $this->outilsString = $outilsString;
    }

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
                    'choices' => ['vert' => 'vert', 'jaune' => 'jaune', 'rouge' => 'rouge', 'gris' => 'gris'],
                    'multiple' => true,
                    'expanded' => true
                )
            )
            ->add(
                'livraison',
                'entity',
                array(
                    'class' => 'FIANETSceauBundle:LivraisonType',
                    'choice_label' => 'libelle',
                    'empty_value' => 'livraisons_tous',
                    'query_builder' => function(LivraisonTypeRepository $ltr) {
                        return $ltr->menuDeroulant();
                    },
                    'required' => false,
                    'translation_domain' => 'livraisons',
                    'choice_translation_domain' => 'livraisons'
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
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->outilsString->trierListeStringsSelonLocale(
            $view->children['livraison']->vars['choices'],
            'livraisons'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fianet_sceaubundle_questionnaires_liste';
    }
}
