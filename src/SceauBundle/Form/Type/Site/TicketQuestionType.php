<?php

namespace SceauBundle\Form\Type\Site;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SceauBundle\Entity\TicketActeur;
use SceauBundle\Entity\TicketType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TicketQuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', new TicketTypeQuestionType());

        $formModifier = function(FormInterface $form) {
            $form
                ->add('lastName', 'text', [
                    'label'    => 'form.ticket.lastName',
                    'mapped'   => false,
                ])
                ->add('firstName', 'text', [
                    'required' => false,
                    'label'    => 'form.ticket.firstName',
                    'mapped'   => false,
                ])
                ->add('email', 'repeated', [
                    'type'     => 'email',
                    'mapped'   => false,
                    'first_options'  => ['label' => 'form.ticket.email'],
                    'second_options' => ['label' => 'form.ticket.email_confirmation'],
                ])
                ->add('phone', 'text', [
                    'label'    => 'form.ticket.phone',
                    'mapped'   => false,
                ])
                ->add('question', 'textarea', [
                    'label'    => 'form.ticket.question',
                ])
                ->add('submit', 'submit', [
                    'label'    => 'form.ticket.submit'
                ])
            ;
        };

        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifier) {
                $ticketType = $event->getForm()->getData();

                if ($ticketType->getId() && $ticketType->isForm()) {
                    $formModifier($event->getForm()->getParent());
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
            'data_class'         => 'SceauBundle\Entity\Ticket',
            'translation_domain' => 'site_contact_ticket',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ticket';
    }
}
