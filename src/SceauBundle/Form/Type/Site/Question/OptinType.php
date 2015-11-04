<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class OptinType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('optin', 'checkbox', [
                'label'    => 'J\'accepte de recevoir les offres des partenaires de FIA-NET adaptées à mon profil.',
               'required' => false,
            ])
        ;

        $builder->get('optin')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getForm()->getData();

                if ($data) {
                    $event->getForm()->getParent()
                        ->add('civility', 'choice', [
                            'choices'  => [
                                'Mme',
                                'Mlle',
                                'M.',
                            ],
                            'expanded' => true,
                            'multiple' => false,
                        ])
                        ->add('firstName', 'text', [
                            'label' => 'Nom :',
                        ])
                        ->add('lastName', 'text', [
                            'label' => 'Prénom :',
                        ])
                    ;
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_question_optin';
    }
}