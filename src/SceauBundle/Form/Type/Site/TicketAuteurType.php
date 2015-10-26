<?php

namespace SceauBundle\Form\Type\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TicketAuteurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', 'text', [
                'label'    => 'form.ticket.lastName',
            ])
            ->add('firstName', 'text', [
                'required' => false,
                'label'    => 'form.ticket.firstName',
            ])
            ->add('email', 'repeated', [
                'type'     => 'email',
                'first_options'  => ['label' => 'form.ticket.email'],
                'second_options' => ['label' => 'form.ticket.email_confirmation'],
            ])
            ->add('phone', 'text', [
                'label'    => 'form.ticket.phone',
            ])
        ;   
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'SceauBundle\Entity\TicketAuteur',
            'translation_domain' => 'site_contact_ticket',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ticket_auteur';
    }
}
