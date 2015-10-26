<?php

namespace SceauBundle\Form\Type\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SceauBundle\Entity\TicketType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TicketTypeQuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('actor', 'choice', [
                'placeholder'   => '-----------------------------',
                'empty_data'    => null,
                'label'         => 'form.ticket_type.acteur',
                'choices'       => TicketType::$ACTORS,
            ]);
        ;

        $formModifier = function (FormInterface $form, $actor) {
            $form->add('id', 'choice', [
                'placeholder'   => '-----------------------------',
                'empty_data'    => null,
                'label'         => 'form.ticket_type.id',
                'choices'       => TicketType::getAvailableTypes($actor),
            ]);
        };

        $builder->get('actor')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $actor = $event->getForm()->getData();

                if ($actor) {
                    $formModifier($event->getForm()->getParent(), $actor);
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'SceauBundle\Entity\TicketType',
            'translation_domain' => 'site_contact_ticket',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ticket_type';
    }
}
