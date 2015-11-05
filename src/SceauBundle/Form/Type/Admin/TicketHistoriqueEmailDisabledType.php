<?php

namespace SceauBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;


class TicketHistoriqueEmailDisabledType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date','date', array(
                'widget'    => 'single_text',
                'disabled'  => true,
                'label'     => 'Date',
            ))
            ->add('mailFrom','text',array(
                'disabled' => true,
                'label'    => 'Expéditeur',
            ))
            ->add('mailTo','text',array(
                'disabled' => true,
                'label'    => 'Destinataire',
            ))
            ->add('mailSubject','text',array(
                'disabled' => true,
                'label'    => 'Sujet',
            ))
            ->add('mailBody','textarea',array(
                'disabled' => true,
                'label'    => 'Message',
            ))
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
        return null;
    }
}
