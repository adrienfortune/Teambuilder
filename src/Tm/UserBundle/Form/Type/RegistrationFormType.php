<?php

namespace Tm\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('captcha', 'captcha', array(
            'label' => 'Code visuel :',
            'reload' => true,
            'as_url' => true
        ));
    }

    public function getName()
    {
        return 'tmuserbundle_user_registration';
    }
}