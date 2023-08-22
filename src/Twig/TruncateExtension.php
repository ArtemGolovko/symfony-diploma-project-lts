<?php

namespace App\Twig {

    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;

    class TruncateExtension extends AbstractExtension
    {
        /**
         * @return TwigFilter[]
         */
        public function getFilters(): array
        {
            return [
                new TwigFilter('truncate', 'truncate'),
            ];
        }
    }
}

namespace {

    /**
     * @param string $value
     * @param int    $length
     * @param string $append
     *
     * @return string
     */
    function truncate(string $value, int $length, string $append = ''): string
    {
        if (strlen($value) <= $length) {
            return $value;
        }

        return substr($value, 0, $length) . $append;
    }
}
