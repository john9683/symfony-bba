<?php

namespace App\Twig;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AgoExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'getDiff']),
        ];
    }

    /**
     * @var string $value
     * @return string
     */
    public function getDiff(string $value): string
    {
        return Carbon::make($value)->locale('ru')->diffForHumans();

    }
}
