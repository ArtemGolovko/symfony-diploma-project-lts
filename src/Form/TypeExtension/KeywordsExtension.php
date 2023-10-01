<?php

namespace App\Form\TypeExtension;

use App\Form\DataTransformer\KeywordsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KeywordsExtension extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addViewTransformer(new KeywordsTransformer())
            ->add('0', TextType::class)
            ->add('1', TextType::class)
            ->add('2', TextType::class)
            ->add('3', TextType::class)
            ->add('4', TextType::class)
            ->add('5', TextType::class)
            ->add('6', TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
