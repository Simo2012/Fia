<?php

namespace FIANET\SceauBundle\Form\Type;

use FIANET\SceauBundle\Service\OutilsString;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
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
            ->add('libelle', 'text', array('attr' => array('class' => 'libelle')))
            ->add('libelleCourt')
            ->add('questionType');

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $question = $event->getData();

                if (null === $question) {
                    return;
                }

                if ($question->getQuestionType() == null) {
                    /* Aucun type de question n'a été sélectionné */
                    $event->getForm()->remove('libelle');
                    $event->getForm()->remove('libelleCourt');

                } else {
                    $event->getForm()->add(
                        'reponses',
                        'collection',
                        array(
                            'type' => new ReponseType($question->getQuestionType()->getId()),
                            'allow_add' => true,
                            'by_reference' => false
                        )
                    );

                    if ($question->getQuestionType()->getId() == \FIANET\SceauBundle\Entity\QuestionType::NOTATION) {
                        $event->getForm()
                            ->add('valeurMin', 'number', array('precision' => 1))
                            ->add('valeurMax', 'number', array('precision' => 1));
                    }
                }
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->outilsString->trierListeStringsSelonLocale(
            $view->children['questionType']->vars['choices'],
            'questionType'
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Question',
            'validation_groups' => 'globale',
            'cascade_validation' => true
        ));
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'fianet_sceaubundle_question';
    }
}
