<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \SceauBundle\Entity\Reponse[] $responses */
        $responses = $options['responses'];
        $min       = $options['min'];
        $max       = $options['max'];

        foreach ($responses as $response) {
            $builder->add($response->getId(), 'choice', [
                'choices'  => range($min, $max),
                'multiple' => false,
                'expanded' => true,
                'label'    => $response->getLibelle(),
                'mapped'   => false,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'responses' => [],
            'min'       => 0,
            'max'       => 10,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notation';
    }
}