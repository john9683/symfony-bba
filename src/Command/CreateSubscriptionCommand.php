<?php

namespace App\Command;

use App\Service\SubscriptionService;
use App\Factory\SubscriptionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;

class CreateSubscriptionCommand extends Command
{
    public function __construct(
        SubscriptionFactory $factory,
        SubscriptionService $subscriptionService
    )
    {
        parent::__construct();
        $this->factory = $factory;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @var string $defaultName
     */
    protected static $defaultName = 'app:create-subscription';

    /**
     * @var string $defaultDescription
     */
    protected static $defaultDescription = 'Запуск фабрики по производству подписок';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'Конкретные типы подписки для создания или обновления в виде кодов (подряд через запятую без пробелов)'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->subscriptionService->loadSubscriptions(
            $this->factory->getSubscriptionObjectsArray(
                $input->getArgument('type') !== null ? explode(",", $input->getArgument('type')) : null
            )
        );

        /** @var array $newSubscriptions */
        $newSubscriptions = $result[0];

        /** @var array $updatedSubscriptions */
        $updatedSubscriptions = $result[1];

        $console = new SymfonyStyle($input, $output);

        $rowsNew = [];
        $countNew = 1;
        foreach ($newSubscriptions as $subscription) {
            $rowsNew[] = [$countNew++, $subscription->getDescription()['title']];
        }

        $tableNew = new Table($output);
        $tableNew
            ->setHeaders(['#', 'Назание загруженных типов подписки'])
            ->setRows($rowsNew)
        ;

        $rowsUpdated = [];
        $countUpdated = 1;
        foreach ($updatedSubscriptions as $subscription) {
            $rowsUpdated[] = [$countUpdated++, $subscription->getDescription()['title']];
        }

        $tableUpdated = new Table($output);
        $tableUpdated
            ->setHeaders(['#', 'Назание обновлённых типов подписки'])
            ->setRows($rowsUpdated)
        ;

        if (count($newSubscriptions) > 0) {
            $console->success(
                "Типы новых подписок в количестве " . count($newSubscriptions) . " штуки залиты в базу данных:");
                $tableNew->render();
        } else {
            $console->warning(
                "Новых типов подписок в базу данных не загружено");
        }

        if (count($updatedSubscriptions) > 0) {
            $console->success(
                "Типы подписок в количестве " . count($updatedSubscriptions) . " штуки обновлены в базе данных:");
                $tableUpdated->render();
        } else {
            $console->warning(
                "Типы подписок не были обновлены");
        }

        return Command::SUCCESS;
    }
}
