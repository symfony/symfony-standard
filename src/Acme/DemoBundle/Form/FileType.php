<?php

namespace Acme\DemoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
                'required'      => false,
                'label'         => 'Select a file to be uploaded',
                'constraints'   => array(
                    new File(
                        array(
                            'maxSize' => '1024k',
                        )
                    )
                )
            )
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function(FormEvent $event) {
                $data = $event->getData();
                print_r(null);
                die();
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        return $resolver->setDefaults(array());
    }

    public function getName()
    {
        return 'FileType';
    }
}
