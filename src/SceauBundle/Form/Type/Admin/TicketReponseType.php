<?php

namespace SceauBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SceauBundle\Entity\Repository\TicketReponseModeleRepository;
use Doctrine\ORM\EntityRepository;


class TicketReponseType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modeleType','entity', [
                'class'         => 'SceauBundle:TicketReponseModele',
                'empty_value'   => '-- Choisissez une réponse type --',
                'choice_label'  => 'type',
                'property'      => 'type',
            ])
            ->add('expediteur' , 'text', [
                'data' => 'sceau-de-confiance@fia-net.fr'
            ])
            ->add('sujet', 'text')
            ->add('message', 'textarea')
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
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return null;
    }
}
