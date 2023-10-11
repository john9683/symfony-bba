<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Subscription;
use App\Events\SettingSubscriptionEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Factory\SubscriptionFactory;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormInterface;

class SubscriptionService
{
    public function __construct(
        LoggerInterface $checkSubscriptionLogger,
        EntityManagerInterface $em,
        SubscriptionFactory $subscriptionFactory,
        Security $security,
        EventDispatcherInterface $dispatcher
    ) {
        $this->logger = $checkSubscriptionLogger;
        $this->em = $em;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->security = $security;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Subscription $subscription
     * @return Subscription
     */
    private function add(Subscription $subscription): Subscription
    {
        $this->em->persist($subscription);
        $this->em->flush();
        return $subscription;
    }

    /**
     * @param Subscription $item
     * @param Subscription $subscription
     * @return Subscription
     */
    private function update(Subscription $item, Subscription $subscription): Subscription
    {
        $item->setActive($subscription->getDescription()['active']);
        $item->setIsDefault($subscription->getDescription()['isDefault']);
        $item->setLevel($subscription->getDescription()['level']);
        $item->setCode($subscription->getDescription()['code']);
        $item->setDescription($subscription->getDescription());
        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }

    /**
     * @param Subscription[] $subscriptionsArray
     * @return Subscription[]
     */
    public function loadSubscriptions(array $subscriptionsArray): array
    {
        $newSubscriptions = [];
        $updatedSubscriptions = [];

        foreach ($subscriptionsArray as $subscription) {
            $item = $this->em->getRepository(Subscription::class)->findOneBy([
                'showcase' => true,
                'active' => true,
                'code' => $subscription->getCode()
            ]);
            if ($item === null) {
                $newSubscriptions[] = $this->add($subscription);
            }
            if ($item !== null) {
                $updatedSubscriptions[] = $this->update($item, $subscription);
            }
        }
        return [$newSubscriptions, $updatedSubscriptions];
    }

    /**
     * @return Subscription
     */
    public function getDefaultSubscription(): Subscription
    {
        return $this->em->getRepository(Subscription::class)->findOneBy([
            'showcase' => true,
            'active' => true,
            'isDefault' => true
        ]);
    }

    /**
     * @param string $code
     * @return Subscription
     */
    public function getSubscriptionByCode(string $code): Subscription
    {
        return $this->em->getRepository(Subscription::class)->findOneBy([
            'showcase' => true,
            'active' => true,
            'code' => $code
        ]);
    }

    /**
     * @return Subscription[]
     */
    public function getSubscriptionsTypesArray(): array
    {
        return $this->em->getRepository(Subscription::class)->findBy([
            'showcase' => true,
            'active' => true
        ], [
            'level' => 'ASC'
        ]);
    }

    /**
     * @return int
     */
    public function getHighestLevelSubscription(): int
    {
        $index = count($this->getSubscriptionsTypesArray()) - 1;

        return $this->getSubscriptionsTypesArray()[$index]->getLevel();
    }

    /**
     * @param User $user
     * @return void
     */
    public function createUserDefaultSubscription(User $user): void
    {
        $subscription = $this->getDefaultSubscription();

        $userSubscription = $this->subscriptionFactory->makeSubscription(
            $user,
            false,
            true,
            $subscription->isActive(),
            $subscription->getLevel(),
            $subscription->getCode(),
            $subscription->getDescription(),
        );

        $this->em->persist($userSubscription);
        $this->em->flush();

        $this->dispatcher->dispatch(new SettingSubscriptionEvent($user, 'default'));
    }

    /**
     * @return Subscription[]
     */
    public function getCurrentUserSubscriptionsArray(): array
    {
        $userId = $this->security->getUser()->getId();

        return $this->em->getRepository(Subscription::class)->findBy(['owner' => $userId, 'active' => true]);
    }

    /**
     * @return Subscription
     */
    public function getCurrentUserSubscription(): Subscription
    {
          return $this->getCurrentUserSubscriptionsArray()[0];
    }

    /**
     * @return void
     */
    public function desactivateUserSubscriptions(): void
    {
        $userSubscriptionsArray = $this->getCurrentUserSubscriptionsArray();

        foreach ($userSubscriptionsArray as $userSubscription)
        {
            $userSubscription->setActive(false);
            $this->em->persist($userSubscription);
        }
        $this->em->flush();
    }
    /**
     * @param string $code
     * @return void
     */
    public function createUserSubscriptionByCode(string $code): void
    {
        $this->desactivateUserSubscriptions();

        /** @var User $user */
        $user = $this->security->getUser();

        $subscription = $this->getSubscriptionByCode($code);

        $userSubscription = $this->subscriptionFactory->makeSubscription(
            $user,
            false,
            false,
            $subscription->isActive(),
            $subscription->getLevel(),
            $subscription->getCode(),
            $subscription->getDescription(),
        );

        $this->em->persist($userSubscription);
        $this->em->flush();

        $this->dispatcher->dispatch(new SettingSubscriptionEvent($user, 'buy'));

    }

    /**
     * @param FormInterface|null $form
     * @return void
     */
    public function handlerFormRequest(?FormInterface $form): void
    {
        if ($form->isSubmitted()) {
            $this->createUserSubscriptionByCode($form->get('code')->getData());
        }
    }

    /**
     * @return array
     */
    public function getWarning(): array
    {
        if ($this->getCurrentUserSubscription()->getDescription()['limits']['daysPeriod'] === 'noLimit'
        ) {
            return [false, 0];
        } else {
            $shift = 1;
            $subscriptionPeriod = $this->getCurrentUserSubscription()->getDescription()['limits']['daysPeriod'];
            $warningFrom = $this->getCurrentUserSubscription()->getDescription()['limits']['warningDays'] + $shift;

            return [
                Carbon::now() >= Carbon::createFromFormat('Y.m.d',
                    $this
                        ->getCurrentUserSubscription()
                        ->getCreatedAt()
                        ->format('Y.m.d'))
                    ->addDays($subscriptionPeriod - $warningFrom),
                Carbon::createFromFormat('Y.m.d',
                    $this
                        ->getCurrentUserSubscription()
                        ->getCreatedAt()
                        ->format('Y.m.d'))->addDays($subscriptionPeriod)
                    ->diffInDays(Carbon::now())
            ];
        }
    }

    /**
     * @return Carbon
     */
    public function getDateEnd(): Carbon
    {
        $subscriptionPeriod = $this->getCurrentUserSubscription()->getDescription()['limits']['daysPeriod'];

        return Carbon::createFromFormat('d.m.Y',
            $this
                ->getCurrentUserSubscription()
                ->getCreatedAt()
                ->format('d.m.Y'))
            ->addDays($subscriptionPeriod)
            ;
    }

    /**
     * @return array
     */
    public function getUsersActiveNotDefaultSubscriptionsArray(): array
    {
        return $this->em->getRepository(Subscription::class)->findBy(
            ['showcase' => false, 'active' => true, 'isDefault' => false],
        );
    }

    /**
     * @return bool
     */
    public function checkAccessToMorphExtension(): bool
    {
        return $this->getCurrentUserSubscription()->getDescription()['limits']['morphExtension'];
    }

    /**
     * @return int
     */
    public function checkQuantityWords(): int
    {
        return $this->getCurrentUserSubscription()->getDescription()['limits']['quantityWords'];
    }

    /**
     * @return bool
     */
    public function checkLimitForCreateArticle(): bool
    {
        $limitForArticles = $this->getCurrentUserSubscription()->getDescription()['limits']['articlesPerPeriod'];
        $limitedPeriod = $this->getCurrentUserSubscription()->getDescription()['limits']['limitedPeriod'];

        if ($limitForArticles === 'noLimit') {return true;}

        $lastArticles = $this->em->getRepository(Article::class)->findBy(
            ['author' => $this->security->getUser()->getId()],
            ['createdAt' => 'desc'],
            $limitForArticles
        );

        if ($lastArticles === null || count($lastArticles) === 0 || count($lastArticles) === 1) {return true;}

        $period = Carbon::createFromFormat('Y-m-d H:i:s',
                $lastArticles[$limitForArticles - 1]->getCreatedAt()->format('Y-m-d H:i:s'))->diffInSeconds(Carbon::now()
            );

        if ($period > $limitedPeriod) {return true;} else {return false;}
    }

    /**
     * @return array
     */
    public function checkPeriodUserSubscription(): array
    {
        $result = [];
        $subscriptionsArray = $this->getUsersActiveNotDefaultSubscriptionsArray();

        foreach ($subscriptionsArray as $subscription) {

            /** @var Subscription $subscription */
            $subscriptionPeriod = $subscription->getDescription()['limits']['daysPeriod'];

            if (Carbon::createFromFormat('Y-m-d H:i:s',
                $subscription
                    ->getCreatedAt()
                    ->format('Y-m-d H:i:s'))->addDays($subscriptionPeriod)
                < Carbon::now()
            ) {
                $subscription->setActive(false);
                $this->em->persist($subscription);
                $this->em->flush();
                $this->logger->warning(
                    'Пользователю ID: '
                    . $subscription->getOwner()->getId() .
                    ' установлена дефолтная подписка'
                );

                $this->createUserDefaultSubscription($subscription->getOwner());

                $result[] = $subscription->getOwner()->getId();
            }
        }

        if(count($result) === 0) {
            $this->logger->info( 'Проверка срока действия подписок: пользователей с истекшей подпиской нет' );
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function checkAccessToModulePage(): bool
    {
        return $this->getCurrentUserSubscription()->getDescription()['limits']['moduleLayout'];
    }

    /**
     * @return int
     */
    public function checkLimitImages(): int
    {
        return $this->getCurrentUserSubscription()->getDescription()['limits']['images'];
    }
}