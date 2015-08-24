<?php

namespace FIANET\SceauBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseType extends AbstractType
{
    protected $questionType_id;

    public function __construct($questionType_id)
    {
        $this->questionType_id = $questionType_id;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Reponse',
            'validation_groups' => 'globale'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fianet_sceaubundle_reponse';
    }
}
