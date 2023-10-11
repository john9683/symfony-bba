<?php

namespace App\Twig;

use App\Entity\Images;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\Extension\RuntimeExtensionInterface;

class ImagesExtension extends AbstractExtension implements RuntimeExtensionInterface
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('imageSrc', [$this, 'getImageSrc']),
        ];
    }

    /**
     * @param Images|null $images
     * @param int|null $index
     * @return string
     */
    public function getImageSrc(?Images $images, ?int $index = null): string
    {
        if ($images === null || $index === null || $index >= count($images->getFilesNames())) {
            $src = 'default_250x250.png';
        } else {
            $src =  $images->getFilesNames()[$index];
        }

        return $src;
    }
}
