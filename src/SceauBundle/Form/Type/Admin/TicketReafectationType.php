<?php

namespace SceauBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SceauBundle\Entity\Ticket;
use SceauBundle\Form\Type\Admin\TicketCategorieType;

class TicketReafectationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categorie', new TicketCategorieType(),[
                'label' => null,
            ])
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