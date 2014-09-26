<?php

namespace Tm\TeambuilderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChampionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',                'text')
            ->add('caracteristiques', 'entity', array(
                'class' => 'TmTeambuilderBundle:Caracteristique',
                'property' => 'code',
                'multiple' => true,
                'expanded' => false,
            ))
            ->add('roles', 'entity', array(
                'class' => 'TmTeambuilderBundle:Role',
                'property' => 'code',
                'multiple' => true,
                'expanded' => false,
            ))
            ->add('typeAttaques', 'entity', array(
                'class' => 'TmTeambuilderBundle:TypeAttaque',
                'property' => 'code',
                'multiple' => true,
                'expanded' => false,
            ))
            ->add('contres', 'entity', array(
                'class' => 'TmTeambuilderBundle:Champion',
                'property' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ))
            ->add('file', 'file');
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
        return 'tm_teambuilderbundle_championtype';
    }
}
