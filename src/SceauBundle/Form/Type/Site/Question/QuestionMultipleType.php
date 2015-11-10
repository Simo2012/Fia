<?php

namespace SceauBundle\Form\Type\Site\Question;

use SceauBundle\Form\Type\Site\QuestionnaireReponseType;
use Symfony\Component\Form\FormBuilderInterface;

class QuestionMultipleType extends QuestionnaireReponseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($builder->has('livraison')) {
            $builder->remove('livraison');
        }
        if ($builder->has('cgu')) {
            $builder->remove('cgu');
        }
        $builder->remove('optin');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_question_multiple';
    }
}