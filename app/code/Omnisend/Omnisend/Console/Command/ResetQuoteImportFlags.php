<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\Quote as QuoteResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetQuoteImportFlags extends Command
{
    const COMMAND_NAME = 'omnisend:import-status:reset-quote-flags';
    const COMMAND_DESCRIPTION = 'Resets imported quotes flag values.';

    /**
     * @var QuoteResource
     */
    private $quoteResource;

    /**
     * @param QuoteResource $quoteResource
     * @param null $name
     */
    public function __construct(QuoteResource $quoteResource, $name = null)
    {
        $this->quoteResource = $quoteResource;

        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Resetting quote flags...');
        $affectedRows = $this->quoteResource->resetIsImportedValues();
        $output->writeln('Action complete, total affected rows - ' . $affectedRows . '.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
