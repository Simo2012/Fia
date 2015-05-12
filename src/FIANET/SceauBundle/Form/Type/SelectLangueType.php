<?php

namespace FIANET\SceauBundle\Form\Type;

use FIANET\SceauBundle\Validator\Constraints\SiteId;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SelectLangueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'langue',
                'entity',
                array(
                    'class' => 'FIANETSceauBundle:Langue',
                    'property' => 'libelle'
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'select_langue';
    }
}
