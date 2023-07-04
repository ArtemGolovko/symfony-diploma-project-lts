<?php

namespace App\Form\TypeExtension;

use App\Form\DataTransformer\RepeatedPasswordTransformer;
use App\Validator\ConfirmPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RepeatedPasswordExtension extends AbstractType
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
            ->addViewTransformer(new RepeatedPasswordTransformer())
            ->add('password', PasswordType::class, [
                'constraints' => $options['constraints'],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'constraints' => [
                    new ConfirmPassword(),
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new NotBlank(['message' => 'Пароль не может быть пустым']),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Пароль должен иметь длину хотя бы в шесть символов',
                ]),
            ],
        ]);
    }
}
