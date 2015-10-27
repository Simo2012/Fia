<?php

namespace SceauBundle\Form\Type\Admin\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SceauBundle\Entity\Repository\TicketReponseModeleRepository;
use Doctrine\ORM\EntityRepository;
use SceauBundle\Entity\TicketType;

class TicketFiltersType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('etat', 'choice', [
                'choices'   => [
                    null    => 'Tous',
                    false   => 'En attente',
                    true    => 'Traités'
                ]
            ])
            ->add('type','choice', [
                'choices'       => TicketType::$TYPES,
            ])
            ->add('destinataire')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefaults(array(
        //     //'data_class' => 'SceauBundle\Entity\Actualite'
        // ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return null;
    }
}
