<?php declare(strict_types=1);

namespace Svea\Checkout\Console\Command;

use Svea\Checkout\Cron\CheckPendingPayments;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCampaigns extends Command
{
    /**
     * @var CheckPendingPayments
     */
    private $fetchCampaignsCron;

    /**
     * FetchCampaigns constructor.
     *
     * @param CheckPendingPayments $checkPendingPaymentsAction
     */
    public function __construct(
        ?string $name = null,
        CheckPendingPayments $checkPendingPaymentsAction
    ) {
        $this->fetchCampaignsCron = $checkPendingPaymentsAction;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('svea:campaign:fetch');
        $this->setDescription('Svea: Fetch product campaigns.');

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Starting fetching of campaigns.</comment>');
        $this->fetchCampaignsCron->execute();
        $output->writeln('<info>Finished</info>');
    }
}
