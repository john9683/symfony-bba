<?php

namespace App\Import;

class SubscriptionTypes
{
    /**
     * @var array|string[] $subscriptionArray
     */
    private array $subscriptionArray = [

        'free' => [
            'code' => 'free',
            'active' => true,
            'isDefault' => true,
            'level' => 0,
            'title' => 'Free',
            'price' => 0,
            'periodForPrice' => 'неделя',
            'periodForLimit' => 'noLimit',
            'advantages' => [
                [
                    'feature' => 'Безлимитная генерация статей для вашего аккаунта',
                    'access' => true,
                    'strong' => false
                ],
                [
                    'feature' => 'Базовые возможности генератора',
                    'access' => true,
                    'strong' => false
                ],
                [
                    'feature' =>  'Продвинутые возможности генератора',
                    'access' => false,
                    'strong' => false
                ],
                [
                    'feature' =>  'Свои модули',
                    'access' => false,
                    'strong' => false
                ],
            ],
            'limits' => [
                'daysPeriod' => 'noLimit',
                'warningDays' => 'no',
                'moduleLayout' => false,
                'morphExtension' => false,
                'articlesPerPeriod' => 2,
                'limitedPeriod' => 3600,
                'images' => 0,
                'quantityWords' => 1,
            ],
        ],

        'plus' => [
            'code' => 'plus',
            'active' => true,
            'isDefault' => false,
            'level' => 1,
            'title' => 'Plus',
            'price' => 9,
            'periodForPrice' => 'неделя',
            'periodForLimit' => 'week',
            'advantages' => [
                [
                    'feature' => 'Безлимитная генерация статей для вашего аккаунта',
                    'access' => true,
                    'strong' => false
                ],
                [
                    'feature' => 'Базовые возможности генератора',
                    'access' => true,
                    'strong' => false
                ],
                [
                    'feature' =>  'Продвинутые возможности генератора',
                    'access' => true,
                    'strong' => false
                ],
                [
                    'feature' =>  'Свои модули',
                    'access' => false,
                    'strong' => false
                ],
            ],
            'limits' => [
                'daysPeriod' => 7,
                'warningDays' => 3,
                'moduleLayout' => false,
                'morphExtension' => true,
                'articlesPerPeriod' => 2,
                'limitedPeriod' => 3600,
                'images' => 5,
                'quantityWords' => 2,
            ],
        ],

        'pro' => [
            'code' => 'pro',
            'active' => true,
            'isDefault' => false,
            'level' => 2,
            'title' => 'Pro',
            'price' => 49,
            'periodForPrice' => 'неделя',
            'warningDays' => 3,
            'advantages' => [
                [
                    'feature' => 'Безлимитная генерация статей для вашего аккаунта',
                    'access' => true,
                    'strong' => true
                ],
                [
                    'feature' => 'Базовые возможности генератора',
                    'access' => true,
                    'strong' => false
                ],
                [
                    'feature' =>  'Продвинутые возможности генератора',
                    'access' => true,
                    'strong' => false
                ],
                [
                    'feature' =>  'Свои модули',
                    'access' => true,
                    'strong' => false
                ],
            ],
            'limits' => [
                'daysPeriod' => 7,
                'warningDays' => 3,
                'moduleLayout' => true,
                'morphExtension' => true,
                'articlesPerPeriod' => 'noLimit',
                'limitedPeriod' => 3600,
                'images' => 5,
                'quantityWords' => 3,
            ],
        ],

    ];

    /**
     * @return array|array[]
     */
    public function getSubscriptionArray(): array
    {
        return $this->subscriptionArray;
    }
}