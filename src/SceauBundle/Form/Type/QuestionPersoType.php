<?php

namespace SceauBundle\Form\Type;

use SceauBundle\Entity\QuestionTypeRepository;
use IntlDateFormatter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class QuestionPersoType extends QuestionType
{
    /**
     * {@inheritdoc}
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
                    'format' => IntlDateFormatter::SHORT,
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
                    'format' => IntlDateFormatter::SHORT,
                    'required' => true,
                    'constraints' => new Date(array('message' => 'constraints.date_invalide'))
                )
            )->add(
                'questionType',
                'entity',
                array(
                    'class' => 'SceauBundle:QuestionType',
                    'choice_label' => 'libelle',
                    'empty_value' => 'choisir_valeur',
                    'translation_domain' => 'questionType',
                    'choice_translation_domain' => 'questionType',
                    'required' => true,
                    'query_builder' => function (QuestionTypeRepository $repo) {
                        return $repo->typesPersonnalisablesQueryBuilder();
                    }
                )
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $question = $event->getData();

                if (null === $question) {
                    return;
                }

                if ($question->getQuestionType() == null) {
                    /* Aucun type de question n'a été sélectionné */
                    $event->getForm()->remove('dateDebut');
                    $event->getForm()->remove('dateFin');

                } else {
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
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\SceauBundle\Entity\Question',
            'validation_groups' => 'perso',
            'cascade_validation' => true
        ));
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'sceaubundle_question_perso';
    }
}
