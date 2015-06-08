<?php

namespace FIANET\SceauBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReponseType extends AbstractType
{
    private $globale;

    /**
     * @param bool $globale true si la réponse est liée à une question globale ou false pour une question personnalisée
     */
    public function __construct($globale)
    {
        $this->globale = $globale;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle');

        if ($this->globale) {
            $builder
                ->add('libelleCourt')
                ->add('ordre')
                ->add('precision')
                ->add('actif')
                ->add('question');
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FIANET\SceauBundle\Entity\Reponse'
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
