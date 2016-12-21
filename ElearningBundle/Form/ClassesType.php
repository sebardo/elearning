<?php

namespace ElearningBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ClassesType extends AbstractType
{
    protected $formConfig;

    public function __construct($formConfig=array())
    {
        $this->formConfig = $formConfig;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('name')
            ->add('course', EntityType::class, array(
                'choice_label' => 'name', // Assuming that the entity has a "name" property
                'class'    => 'ElearningBundle:Course'
            ))
            ->add('teachers', EntityType::class, array(
                'required' => false,
                'choice_label' => 'fullName', // Assuming that the entity has a "name" property
                'class'    => 'CoreBundle:Actor',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  {
                                        return $er->createQueryBuilder('a')
                                                    ->select('a')
                                                    ->leftJoin('a.roles', 'r')
                                                    ->andWhere('r.role = :role')
                                                    ->setParameters(array('role' => 'ROLE_MANAGER'))
                                                    ;
                                    },
                'expanded' => false,
                'multiple'  => true,
            ))
            ->add('students', EntityType::class, array(
                'required' => false,
                'choice_label' => 'fullName', // Assuming that the entity has a "name" property
                'class'    => 'CoreBundle:Actor',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                                        return $er->createQueryBuilder('s')
                                                    ->select('s')
                                                    ->leftJoin('s.roles', 'r')
                                                    ->where('r.role = :role')
                                                    ->setParameters(array('role' => 'ROLE_USER'))
                                                    ;

                                    },
                'expanded' => false,
                'multiple'  => true
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ElearningBundle\Entity\Classes'
        ));
    }

}
