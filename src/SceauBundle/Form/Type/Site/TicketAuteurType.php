<?php

namespace SceauBundle\Form\Type\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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

        // A tester avec un utilisateur authentifié, pour le moment ça ne marche pas.
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        //     $user = $this->tokenStorage->getToken()->getUser();
            
        //     if ($user) {
        //         $form = $event->getData();
        //         $form['lastName'] = $user->getLastName();
        //         $form['firstName'] = $user->getFirstName();
        //         $form['email'] = $user->getEmail();
        //         $form['phone'] = $user->getPhone();

        //         $event->setData($form);
        //     }
        // })  
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
