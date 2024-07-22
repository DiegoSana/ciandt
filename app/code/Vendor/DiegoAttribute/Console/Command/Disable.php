<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vendor\DiegoAttribute\Api\DiegoAttributeManagementInterface;

class Disable extends Command
{
    public const COMMAND_NAME = 'diegoattribute:settings:disable';
    protected const STORE_OPTION = "store";

    /**
     * @param DiegoAttributeManagementInterface $attributeManagement
     */
    public function __construct(
        protected DiegoAttributeManagementInterface $attributeManagement
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription("Disable diego attribute");
        $this->setDefinition([
            new InputOption(self::STORE_OPTION, 's', InputOption::VALUE_OPTIONAL, "Use specified store id", 0),
        ]);

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        try {

            $store = $input->getOption(self::STORE_OPTION);
            $this->attributeManagement->setStatus(false, (int) $store);
            $output->writeln("Diego attribute value was disabled.");

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Cli::RETURN_FAILURE;
        }
    }
}
