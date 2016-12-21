<?php

namespace ElearningBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CourseType extends AbstractType
{
    protected $formConfig;
    
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConfig = $options;
        //Site
        $siteConfig = array(
                'choice_label' => 'name', // Assuming that the entity has a "name" property
                'class'    => 'CoreBundle:Site',
            );
        
        if (isset($this->formConfig['siteIds'])) {
            $siteIds = $this->formConfig['siteIds'];
            $siteConfig  = array_merge($siteConfig,array(
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($siteIds) {
                                        return $er->createQueryBuilder('s')
                                                ->select('s')
                                                ->where('s.id IN (:siteIds)')
                                                ->setParameter('siteIds', array_values($siteIds))
                                                ;

                                    }
             ));
        }
        $builder
            ->add('name')
            ->add('site', EntityType::class, $siteConfig)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ElearningBundle\Entity\Course',
            'siteIds' => null
        ));
    }
}
