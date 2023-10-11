<?php

namespace App\Factory;

use App\Entity\Subscription;
use App\Entity\User;
use App\Import\SubscriptionTypes;

class SubscriptionFactory
{
    public function __construct(
        SubscriptionTypes $subscriptionTypes
    ) {
        $this->subscriptionTypes = $subscriptionTypes;
    }

    /**
     * @param User|null $owner
     * @param bool $showcase
     * @param bool $isDefault
     * @param bool $active
     * @param int $level
     * @param string $code
     * @param array $description
     * @return Subscription
     */
    public function makeSubscription(
        ?User $owner,
        bool $showcase,
        bool $isDefault,
        bool $active,
        int $level,
        string $code,
        array $description
    ): Subscription {
        return new Subscription(
            $owner,
            $showcase,
            $isDefault,
            $active,
            $level,
            $code,
            $description
        );
    }

    /**
     * @param array|null $codes
     * @return array
     */
    public function getSubscriptionObjectsArray(?array $codes): array
    {
        $subscriptionObjectsArray = [];
        $subscriptions = $this->subscriptionTypes->getSubscriptionArray();

        if ($codes !== null) {
            foreach ($subscriptions as $subscription) {
                foreach ($codes as $code) {
                    if ($code === $subscription['code']) {
                        $subscriptionObjectsArray[] = $this->makeSubscription(
                            null,
                            true,
                            $subscription['isDefault'],
                            $subscription['active'],
                            $subscription['level'],
                            $code,
                            $subscription
                        );
                    }
                }
            }
        } else {
            foreach ($subscriptions as $subscription) {
                $subscriptionObjectsArray[] = $this->makeSubscription(
                    null,
                    true,
                    $subscription['isDefault'],
                    $subscription['active'],
                    $subscription['level'],
                    $subscription['code'],
                    $subscription
                 );
            }
        }

        return $subscriptionObjectsArray;
    }
}