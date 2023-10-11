<?php

namespace App\Command;

use App\Service\SubscriptionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckSubscriptionCommand extends Command
{
    public function __construct(
        SubscriptionService $subscriptionService
    )
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @var string $defaultName
     */
    protected static $defaultName = 'app:check-subscription';

    /**
     * @var string $defaultDescription
     */
    protected static $defaultDescription = 'Проверка срока действия подписок пользователей и сброс на дефолт';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->subscriptionService->checkPeriodUserSubscription();

        $console = new SymfonyStyle($input, $output);

        $rows = [];
        $count = 1;
        foreach ($result as $userId) {
            $rows[] = [$count++, $userId];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['#', 'ID пользователя с истекшей подпиской'])
            ->setRows($rows)
        ;

        if (count($result) > 0) {
            $console->success(
                "Пользователям с истекшей подпиской в количестве " . count($result) . " установлена дефолтная подписка:");
            $table->render();
        } else {
            $console->warning(
                "Пользователей с истекшей подпиской нет");
        }

        return Command::SUCCESS;
    }
}
