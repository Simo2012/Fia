<?php

namespace SceauBundle\Form\Type\Site\Question;

class UniqueInlineType extends ChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_question_unique_inline';
    }
}