<?php

namespace FIANET\SceauBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReponseType extends AbstractType
{
    protected $questionType_id;

    public function __construct($questionType_id)
    {
        $this->questionType_id = $questionType_id;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', 'text', array('attr' => array('class' => 'libelle-reponse')))
            ->add('libelleCourt')
            ->add('ordre')
            ->add('actif');

        if ($this->questionType_id == \FIANET\SceauBundle\Entity\QuestionType::CHOIX_MULTIPLE) {
            $builder->add(
                'precision',
                'checkbox',
                array('required' => false)
            );
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Reponse',
            'validation_groups' => 'globale'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fianet_sceaubundle_reponse';
    }
}
