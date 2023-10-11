<?php

namespace App\Twig;

use App\Service\SubscriptionService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\Security\Core\Security;

class MorphExtension extends AbstractExtension
{
    public function __construct(
        Security $security,
        SubscriptionService $subscriptionService
    ) {
        $this->security = $security;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('morph', [$this, 'getKeyword']),
        ];
    }

    /**
     * @param array $keyword
     * @param int $index
     * @return string
     */
    public function getKeyword(array $keyword, int $index): string
    {
        if ($this->security->getUser() === null
            || !$this->subscriptionService->checkAccessToMorphExtension()
            || count($keyword) < 7 ) {
            $index = 0;
        }
        return $keyword[$index];
    }
}
