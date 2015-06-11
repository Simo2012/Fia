<?php

namespace FIANET\SceauBundle\Form\Type;

use Collator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;

class QuestionType extends AbstractType
{
    protected $translator;

    /**
     * @param Translator $translator Instance de Translator
     */
    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
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
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Question',
            'validation_groups' => 'globale',
            'cascade_validation' => true
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
