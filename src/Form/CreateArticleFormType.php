<?php

namespace App\Form;

use App\Entity\Dto\PromotedWord;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use App\Service\ArticleContentGenerator\Theme\ThemeChain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class CreateArticleFormType extends AbstractType implements DataMapperInterface
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
            ->add('keywords', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'data' => array_fill(0, 7, ''),
            ])
            ->add('title', TextType::class)
            ->add('size_begin', NumberType::class)
            ->add('size_end', NumberType::class, [
                'empty_data' => null,
            ])
            ->add('promoted_words', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'data' => ['', '', ''],
                'empty_data' => '',
                'delete_empty' => function (?string $promotedWord) {
                    return empty($promotedWord);
                },
            ])
            ->add('images', FileType::class, [
                'multiple' => true,
                'constraints' => new All(new Image()),
                'mapped' => false,
            ])
            ->setDataMapper($this)
        ;
    }

    /**
     * @param ArticleGenerateOptions|null $viewData
     * @param FormInterface|\Traversable  $forms
     *
     * @return void
     */
    public function mapDataToForms($viewData, $forms): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof ArticleGenerateOptions) {
            throw new UnexpectedTypeException($viewData, ArticleGenerateOptions::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        $sizeBegin = $viewData->getSize()->getBegin();
        $sizeEnd = $viewData->getSize()->getEnd();

        $forms['theme']->setData($viewData->getTheme());
        $forms['keywords']->setData(array_pad($viewData->getKeywords(), 7, ''));
        $forms['title']->setData($viewData->getTitle());
        $forms['size_begin']->setData($sizeBegin);
        $forms['size_end']->setData($sizeEnd === $sizeBegin ? null : $sizeEnd);
        $forms['promoted_words']->setData(
            array_map(function (PromotedWord $promotedWord) {
                return $promotedWord->getWord();
            }, $viewData->getPromotedWords())
        );
    }

    /**
     * @param FormInterface|\Traversable $forms
     * @param ArticleGenerateOptions     $viewData
     *
     * @return void
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $title = $forms['title']->getData();

        $viewData = new ArticleGenerateOptions(
            $forms['theme']->getData(),
            array_filter($forms['keywords']->getData(), function (?string $keyword) {
                return !empty($keyword);
            }),
            new Range($forms['size_begin']->getData(), $forms['size_end']->getData()),
            array_map(function (string $word) {
                return new PromotedWord($word, 2);
            }, $forms['promoted_words']->getData()),
            $title === '' ? null : $title
        );
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
            'empty_data' => null,
        ]);
    }
}
