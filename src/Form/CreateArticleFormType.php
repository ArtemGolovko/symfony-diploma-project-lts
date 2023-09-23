<?php

namespace App\Form;

use App\Entity\Dto\PromotedWord;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use App\Form\TypeExtension\PromotedWordExtension;
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
use Symfony\Component\Validator\Constraints\Count;
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
        $keywords = [];
        /** @var ArticleGenerateOptions|null $data */
        $data = $options['data'] ?? null;

        if (null !== $data) {
            $keywords = $data->getKeywords();
        }

        $keywords = array_pad($keywords, 7, '');

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
                'data' => $keywords,
            ])
            ->add('title', TextType::class)
            ->add('size_begin', NumberType::class)
            ->add('size_end', NumberType::class, [
                'empty_data' => null,
            ])
            ->add('promoted_words', CollectionType::class, [
                'entry_type' => PromotedWordExtension::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => function (PromotedWord $promotedWord): bool {
                    return $promotedWord->isEmpty();
                },
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
        $size = $viewData->getSize();

        $forms['theme']->setData($viewData->getTheme());
        $forms['keywords']->setData(array_pad($viewData->getKeywords(), 7, ''));
        $forms['title']->setData($viewData->getTitle());
        $forms['size_begin']->setData($size->getBegin());
        $forms['size_end']->setData($size->getEnd());
        $forms['promoted_words']->setData($viewData->getPromotedWords());
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

        $viewData = (new ArticleGenerateOptions())
            ->setTheme($forms['theme']->getData())
            ->setKeywords(
                array_filter($forms['keywords']->getData(), function (?string $keyword) {
                    return !empty($keyword);
                })
            )
            ->setTitle($title === '' ? null : $title)
            ->setSize(Range::create($forms['size_begin']->getData(), $forms['size_end']->getData()))
            ->setPromotedWords($forms['promoted_words']->getData())
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
            'empty_data' => null,
        ]);
    }
}
