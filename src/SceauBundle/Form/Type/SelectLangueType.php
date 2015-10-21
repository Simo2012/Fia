<?php

namespace SceauBundle\Form\Type;

use SceauBundle\Entity\Repository\LangueRepository;
use SceauBundle\Service\OutilsString;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class SelectLangueType extends AbstractType
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
                'langue',
                'entity',
                array(
                    'class' => 'SceauBundle:Langue',
                    'choice_label' => 'libelle',
                    'query_builder' => function (LangueRepository $lr) {
                        return $lr->menuDeroulant();
                    },
                    'translation_domain' => 'langues',
                    'choice_translation_domain' => 'langues'
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->outilsString->trierListeStringsSelonLocale(
            $view->children['langue']->vars['choices'],
            'langues'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sceaubundle_select_langue';
    }
}
