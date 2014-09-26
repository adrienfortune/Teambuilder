<?php

namespace Tm\TeambuilderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class RegleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $test = array('testa' => 'testa',
                      'testb' => 'testb'
        );

        $builder
            ->add('nombre',                'integer')
            ->add('priorite',              'integer')
            ->add('caracteristique', 'entity', array(
                'class' => 'TmTeambuilderBundle:Caracteristique',
                'property' => 'code',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'empty_value' => 'Choisissez une option',
            ))
            ->add('role', 'entity', array(
                'class' => 'TmTeambuilderBundle:Role',
                'property' => 'code',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'empty_value' => 'Choisissez une option',
            ))
            ->add('typeAttaque', 'entity', array(
                'class' => 'TmTeambuilderBundle:TypeAttaque',
                'property' => 'code',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'empty_value' => 'Choisissez une option',
            ))
            ->add('operation', 'entity', array(
                'class' => 'TmTeambuilderBundle:Operation',
                'property' => 'code',
                'multiple' => false,
                'expanded' => false,
            ))
            ->add('equipe', 'entity', array(
                'class' => 'TmTeambuilderBundle:Equipe',
                'property' => 'libelle',
                'multiple' => false,
                'expanded' => false,
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tm\TeambuilderBundle\Entity\Regle'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tm_teambuilderbundle_regle';
    }
}
