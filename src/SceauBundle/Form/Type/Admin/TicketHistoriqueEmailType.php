<?php

namespace SceauBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;


class TicketHistoriqueEmailType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reponseModele','entity', [
                'class'         => 'SceauBundle:TicketReponseModele',
                'empty_value'   => '-- Choisissez une réponse type --',
                'choice_label'  => 'type',
                'required'      => false,
            ])
            ->add('mailFrom' , 'text', [
                'data' => 'sceau-de-confiance@fia-net.fr'
            ])
            ->add('mailSubject', 'text')
            ->add('mailBody', 'textarea')
            ->add('save', 'submit', [
                'attr' => ['class' => 'submit'],
                'label' => "Valider",
                'attr' => ['class' => 'btn btn-green']
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SceauBundle\Entity\TicketHistoriqueEmail'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sceaubundle_ticket_historique_email';
    }
}
