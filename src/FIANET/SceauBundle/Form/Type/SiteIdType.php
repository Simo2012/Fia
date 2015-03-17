<?php

namespace FIANET\SceauBundle\Form\Type;

use FIANET\SceauBundle\Validator\Constraints\SiteId;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SiteIdType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site_id', 'text', array('constraints' => array(new SiteId())))
            ->add('valider', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_id';
    }
}