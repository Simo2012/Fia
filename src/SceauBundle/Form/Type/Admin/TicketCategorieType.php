<?php

namespace SceauBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SceauBundle\Entity\TicketCategorie;

class TicketCategorieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'choice', [
                'label'   => null,
                'choices' => TicketCategorie::$TYPES,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'SceauBundle\Entity\TicketCategorie',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sceaubundle_ticket_categorie';
    }
}
