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
    public $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $params = $this->params;
        $builder
            ->add('etat', 'choice', [
                'choices'   => [
                    null    => 'Tous',
                    false   => 'En attente',
                    true    => 'Traités'
                ],
                'data' => $params && $params['etat'] != '' ? (int)$params['etat']: null 

            ])
            ->add('type','choice', [
                'choices'  => [null => '-- Choisissez un type -- ']+TicketType::$TYPES_LABEL,
                'data'     => $params && $params['type'] != '' ? (int)$params['type']: null     
            ])
            ->add('moderateur')
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
