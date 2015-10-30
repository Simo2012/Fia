<?php

namespace SceauBundle\Form\Type\Site\Question;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \SceauBundle\Entity\Reponse $response */
        if (($response = $options['response'])) {
            $builder->add($response->getId(), 'textarea', [
                'required' => false,
                'mapped'   => false,
                'attr'     => [
                    'maxlength' => 500,
                    'rows'      => 5,
                    'cols'      => '98%',
                    'tombola'   => $options['tombola']
                ],
                'label'    => $response->getLibelle(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'response' => null,
            'tombola'  => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'commentaire';
    }
}