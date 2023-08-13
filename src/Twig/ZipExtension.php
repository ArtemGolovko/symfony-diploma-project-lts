<?php

namespace App\Twig {

    use Twig\Extension\AbstractExtension;
    use Twig\TwigFunction;

    class ZipExtension extends AbstractExtension
    {
        /**
         * @return TwigFunction[]
         */
        public function getFunctions(): array
        {
            return [
                new TwigFunction('zip', 'zip'),
            ];
        }
    }
}

namespace {

    /**
     * @param array|Traversable ...$items
     *
     * @return void
     */
    function zip(...$items): array
    {
        /** @var array[] $arrays */
        $arrays = array_map(function ($item) {
            if ($item instanceof Traversable) {
                return iterator_to_array($item, false);
            }

            return $item;
        }, $items);

        $minLength = min(
            array_map(function (array $item) {
                return count($item);
            }, $arrays)
        );

        $result = [];
        for ($i = 0; $i < $minLength; $i++) {
            $result[] = [];
            foreach ($arrays as $array) {
                $result[$i][] = $array[$i];
            }
        }

        return $result;
    }
}
