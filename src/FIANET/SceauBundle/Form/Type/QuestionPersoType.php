<?php

namespace FIANET\SceauBundle\Form\Type;

use Collator;
use FIANET\SceauBundle\Entity\QuestionTypeRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Date;

class QuestionPersoType extends QuestionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('libelleCourt')
            ->remove('questionnaireType');

        $builder
            ->add(
                'dateDebut',
                'date',
                array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'constraints' => new Date(array('message' => 'constraints.date_invalide'))
                )
            )
            ->add(
                'dateFin',
                'date',
                array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'constraints' => new Date(array('message' => 'constraints.date_invalide'))
                )
            )->add(
                'questionType',
                'entity',
                array(
                    'class' => 'FIANETSceauBundle:QuestionType',
                    'property' => 'libelle',
                    'empty_value' => 'choisir_valeur',
                    'translation_domain' => 'questionType',
                    'required' => true,
                    'query_builder' => function(QuestionTypeRepository $repo) {
                        return $repo->typesPersonnalisablesQueryBuilder();
                    }
                )
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) {
                $question = $event->getData();

                if (null === $question) {
                    return;
                }

                if ($question->getQuestionType() == null) {
                    /* Aucun type de question n'a été sélectionné */
                    $event->getForm()->remove('libelle');
                    $event->getForm()->remove('dateDebut');
                    $event->getForm()->remove('dateFin');
                    $event->getForm()->remove('valeurMin');
                    $event->getForm()->remove('valeurMax');
                    $event->getForm()->remove('reponses');

                }

                $event->getForm()->add(
                    'reponses',
                    'collection',
                    array(
                        'type' => new ReponsePersoType($question->getQuestionType()->getId()),
                        'allow_add' => true,
                        'by_reference' => false
                    )
                );
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Question',
            'validation_groups' => 'perso',
            'cascade_validation' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fianet_sceaubundle_question_perso';
    }
}
