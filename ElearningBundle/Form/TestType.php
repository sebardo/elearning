<?php

namespace ElearningBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TestType extends AbstractType
{
    
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Site
        $courseConfig = array(
                'choice_label' => 'name', // Assuming that the entity has a "name" property
                'class'    => 'ElearningBundle:Course',
                'label'    => 'course.singular' 
            );
        
        if (isset($options['siteIds'])) {
            $siteIds = $options['siteIds'];
            $courseConfig  = array_merge($courseConfig,array(
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($siteIds) {
                                        return $er->createQueryBuilder('c')
                                                ->select('c')
                                                ->join('c.site', 's')
                                                ->where('s.id IN (:siteIds)')
                                                ->setParameter('siteIds', array_values($siteIds))
                                                ;

                                    }
             ));
        }
        $builder
            ->add('name')
            ->add('course', EntityType::class, $courseConfig)
            ->add('active')
//            ->add('site', 'entity', $siteConfig)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ElearningBundle\Entity\Test'
        ));
    }

}
