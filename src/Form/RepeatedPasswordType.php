<?php

namespace App\Form;

use App\Form\DataTransformer\RepeatedPasswordTransformer;
use App\Validator\ConfirmPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeatedPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new RepeatedPasswordTransformer())
            ->add('password', PasswordType::class)
            ->add('confirmPassword', PasswordType::class, [
                'constraints' => [
                    new ConfirmPassword()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
