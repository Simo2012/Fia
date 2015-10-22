<?php

namespace SceauBundle\Form\Type\Site;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SceauBundle\Entity\TicketActeur;
use SceauBundle\Entity\TicketType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TicketQuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('acteur', 'entity', array(
                'class'    => 'SceauBundle:TicketActeur',
                'property' => 'libelle',
                //'mapped'   => false,
                'empty_value' => ".......",
            ));
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                $ticket = $event->getData();

            if ($ticket) {
                $form->add('type', 'entity', array(
                    'class'    => 'SceauBundle:TicketType',
                    'property' => 'libelle',
                    'query_builder' => function(EntityRepository $er) use ($ticket) {
                        $qb = $er->createQueryBuilder('tt');
                        return $qb->where($qb->expr()->eq('tt.acteur', $ticket->getId()));
                    }
                ));
            }
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'SceauBundle\Entity\Ticket'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ticket';
    }
}
