<?php

namespace App\EventSubscriber;

use App\Events\SettingSubscriptionEvent;
use App\Service\Mailer;
use App\Service\SubscriptionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SettingSubscriptionSubscriber implements EventSubscriberInterface
{
    public function __construct(Mailer $mailer, SubscriptionService $subscriptionService)
    {
        $this->mailer = $mailer;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param SettingSubscriptionEvent $event
     * @return void
     */
    public function onSettingSubscription(SettingSubscriptionEvent $event): void
    {
        if ($event->getType() === 'buy') {
            $context = [
                'subscriptionTitle' =>  $this->subscriptionService->getCurrentUserSubscription()->getDescription()['title'],
                'type' => 'buy'
                ];

        } elseif ($event->getType() === 'default') {
            $context = [
                'subscriptionTitle' => $this->subscriptionService->getDefaultSubscription()->getDescription()['title'],
                'type' => 'default'
                ];
        }

        $this->mailer->sendSettingSubscriptionMail(
            $event->getUser()->getEmail(),
            $event->getUser()->getFirstName(),
            $context
        );
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SettingSubscriptionEvent::class => 'onSettingSubscription'
        ];
    }
}