<?php


namespace Cminds\Salesrep\Console\Command;

use Cminds\Salesrep\Model\Cron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmulateCron extends Command
{
    /** Command name */
    const NAME = 'cminds:sales-rep:cron';

    /**
     * @var Cron
     */
    protected $cron;

    public function __construct(Cron $cron)
    {
        parent::__construct();
        $this->cron = $cron;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription(
                'send sales rep reports by console'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cron->execute();
        return;
    }
}
