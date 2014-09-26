<?php

namespace Tm\TeambuilderBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChampionEditType extends ChampionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('file')
                ->add('file', 'file', array(
                    'required' => false
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tm\TeambuilderBundle\Entity\Champion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tm_teambuilderbundle_championedittype';
    }
}
