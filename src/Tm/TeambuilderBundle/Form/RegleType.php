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
        $builder
            ->add('nombre',                'integer')
            ->add('typeRegle', 'choice', array(
                'choices' => array(
                    'caracteristique' => 'Caractéristique',
                    'typeAttaque' => 'Type d\'attaque'
                ),
                'required'    => true,
                'empty_value' => 'Choisissez une option',
                'empty_data'  => null,
                'mapped'     => false
            ))
            ->add('caracteristique', 'entity', array(
                'class' => 'TmTeambuilderBundle:Caracteristique',
                'property' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'empty_value' => 'Choisissez une option',
            ))
            ->add('typeAttaque', 'entity', array(
                'class' => 'TmTeambuilderBundle:TypeAttaque',
                'property' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'empty_value' => 'Choisissez une option',
            ))
            ->add('operation', 'entity', array(
                'class' => 'TmTeambuilderBundle:Operation',
                'property' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'required' => true,
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
