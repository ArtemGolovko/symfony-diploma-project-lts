<?php

namespace App\Form;

use App\Entity\Dto\PromotedWord;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Form\TypeExtension\KeywordsExtension;
use App\Form\TypeExtension\PromotedWordExtension;
use App\Form\TypeExtension\RangeExtension;
use App\Service\ArticleContentGenerator\Theme\ThemeChain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Image;

class CreateArticleFormType extends AbstractType
{
    /**
     * @var ThemeChain
     */
    private ThemeChain $themeProvider;

    /**
     * @param ThemeChain $themeProvider
     */
    public function __construct(ThemeChain $themeProvider)
    {
        $this->themeProvider = $themeProvider;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', ChoiceType::class, [
                'choices' => $this->themeProvider->getThemeNames(),
                'choice_label' => function (string $themeName): string {
                    return $themeName;
                },
            ])
            ->add('keywords', KeywordsExtension::class)
            ->add('title', TextType::class, [
                'empty_data' => null,
            ])
            ->add('size', RangeExtension::class)
            ->add('promotedWords', CollectionType::class, [
                'entry_type' => PromotedWordExtension::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => function (PromotedWord $promotedWord): bool {
                    return $promotedWord->getWord() === '' || $promotedWord->getRepetitions() === 0;
                },
                'prototype' => true,
            ])
            ->add('images', FileType::class, [
                'multiple' => true,
                'constraints' => [
                    new All(
                        new Image([
                            'allowPortrait' => false,
                            'allowSquare' => true,
                            'allowLandscape' => true,
                            'maxSize' => '1M',
                        ])
                    ),
                    new Count(['max' => 5]),
                ],
                'mapped' => false,
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
            'data_class' => ArticleGenerateOptions::class,
        ]);
    }
}
