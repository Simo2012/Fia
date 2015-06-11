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
        if ($this->questionType_id == \FIANET\SceauBundle\Entity\QuestionType::COMMENTAIRE) {
            $labelLibelle = 'Libellé du champ de texte : ';
        } else {
            $labelLibelle = 'Libellé : ';
        }

        $builder
            ->add('libelle', 'text', array('label' => $labelLibelle, 'attr' => array('class' => 'libelle')))
            ->add('libelleCourt')
            ->add('ordre')
            ->add('actif');

        if ($this->questionType_id == \FIANET\SceauBundle\Entity\QuestionType::CHOIX_MULTIPLE) {
            $builder->add(
                'precision',
                'checkbox',
                array('label' => 'Ajouter un champ de texte pour préciser la réponse : ', 'required' => false)
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
