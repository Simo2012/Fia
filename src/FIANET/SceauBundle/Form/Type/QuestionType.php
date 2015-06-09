<?php

namespace FIANET\SceauBundle\Form\Type;

use Collator;
use FIANET\SceauBundle\Entity\QuestionTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\Date;

class QuestionType extends AbstractType
{
    private $globale;
    private $translator;

    /**
     * @param bool $globale true pour question globale ou false pour une question personnalisée
     * @param Translator $translator Instance de Translator
     */
    public function __construct($globale, $translator)
    {
        $this->globale = $globale;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle');

        if (!$this->globale) {
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
        }

        $builder
            ->add(
                'reponses',
                'collection',
                array(
                    'type' => new ReponseType($this->globale),
                    'allow_add' => true,
                    'by_reference' => false
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

                } elseif ($question->getQuestionType()->getId() == \FIANET\SceauBundle\Entity\QuestionType::NOTATION) {
                    $event->getForm()
                        ->add('valeurMin')
                        ->add('valeurMax');
                }
            }
        );
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // TODO : créer un service
        $collator = new Collator($this->translator->getLocale());

        usort(
            $view->children['questionType']->vars['choices'],
            function ($a, $b) use ($collator) {
                return $collator->compare(
                    $this->translator->trans($a->label, array(), 'questionType'),
                    $this->translator->trans($b->label, array(), 'questionType')
                );
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        if ($this->globale) {
            $validationGroups = array('globale');
        } else {
            $validationGroups = array('perso');
        }

        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Question',
            'validation_groups' => $validationGroups
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fianet_sceaubundle_question';
    }
}
