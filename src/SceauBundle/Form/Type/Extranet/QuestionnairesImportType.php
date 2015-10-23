<?php

namespace SceauBundle\Form\Type\Extranet;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionnairesImportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', [
                'label'    => 'form.import.file',
                'required' => true,
            ])
            ->add('filename', 'hidden', [
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data || !array_key_exists('file', $data)) {
                return;
            }

            $data['filename'] = time().'_'.$data['file']->getClientOriginalName();
            $event->setData($data);
        });
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation_domain', 'extranet_questionnaires_import');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'questionnaires_import';
    }
}