<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                            'constraints' => [
                                new NotBlank(),
                            ],
                        ])
                        ->add('firstName', 'text', [
                            'label' => 'Nom :',
                            'constraints' => [
                                new NotBlank(),
                            ],
                        ])
                        ->add('lastName', 'text', [
                            'label' => 'Prénom :',
                            'constraints' => [
                                new NotBlank(),
                            ],
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