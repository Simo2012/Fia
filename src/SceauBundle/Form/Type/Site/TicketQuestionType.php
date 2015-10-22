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
        $builder
            ->add('actor', 'choice', [
                'placeholder'   => '-----------------------------',
                'empty_data'    => null,
                'label'         => 'form.ticket.acteur',
                'choices'       => Ticket::$ACTORS,
            ]);
        ;

        $formModifier = function (FormInterface $form, $actor) {
            $form->add('type', 'choice', [
                'placeholder'   => '-----------------------------',
                'empty_data'    => null,
                'label'         => 'form.ticket.type',
                'choices'       => Ticket::getAvailableTypes($actor),
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $form   = $event->getForm();
                /** @var \SceauBundle\Entity\Ticket $ticket */
                $ticket = $event->getData();

                if ($ticket && $ticket->getActor()) {
                    $formModifier($form, $ticket->getActor());
                }
            }
        );

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
            'data_class' => 'SceauBundle\Entity\Ticket',
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
