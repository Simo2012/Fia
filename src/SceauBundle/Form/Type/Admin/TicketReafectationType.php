<?php

namespace SceauBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SceauBundle\Entity\TicketType;

class TicketReafectationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('categorie','choice', [
//                'choices'  => [
//                    1 => 'Avis',
//                    2 => 'Categorie 2'
//                ]
//            ])
            // ->add('destinataire','choice', [
            //     'choices'  => [
            //         1 => 'Admin user 1',
            //         2 => 'Admin user 2',
            //         3 => 'Admin user 3',
            //     ]    
            // ])
            ->add('save','submit')
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
        return 'sceaubundle_ticket_reafectation';
    }
}