<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StripTitleExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('stripTitle', [$this, 'deleteTitle']),
        ];
    }

    /**
     * @param $value
     * @return string
     */
    public function deleteTitle($value): string
    {
        $pattern = [ '#<h1>(.*?)</h1>#' ];

        return preg_replace($pattern,'', $value);
    }
}
