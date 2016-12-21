<?php

namespace ElearningBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AskType extends AbstractType
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
        //Site
        $testConfig=array(
                'property' => 'name', // Assuming that the entity has a "name" property
                'class'    => 'ElearningBundle:Test'
            );
        if (isset($this->formConfig['site'])) {
            $site = $this->formConfig['site'];
            $courseConfig  = array_merge($courseConfig,array(
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($site) {
                                        return $er->createQueryBuilder('t')
                                                ->select('t')
                                                ->join('t.course', 'c')
                                                ->join('c.site', 's')
                                                ->where('s.id = :siteId')
                                                ->setParameter('siteId', $site)
                                                ;

                                    }
             ));
        }
        $builder
            ->add('name')
//            ->add('correct')
//            ->add('site', 'entity', $siteConfig)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ElearningBundle\Entity\Ask'
        ));
    }
}
