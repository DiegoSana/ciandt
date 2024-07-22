<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Console\Command;

use Magento\Catalog\Ui\DataProvider\Product\ProductCollectionFactory;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vendor\DiegoAttribute\Api\DiegoAttributeManagementInterface;
use Magento\Framework\App\State;

class Set extends Command
{
    public const COMMAND_NAME = 'diegoattribute:attribute:set';
    protected const VALUE_ARGUMENT = "value";
    protected const STORE_OPTION = "store";

    /**
     * @param DiegoAttributeManagementInterface $attributeManagement
     * @param State $state
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        protected DiegoAttributeManagementInterface $attributeManagement,
        protected State $state,
        protected ProductCollectionFactory $productCollectionFactory
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription("Change diego attribute values for all products");
        $this->setDefinition([
            new InputArgument(self::VALUE_ARGUMENT, InputArgument::REQUIRED, "Value"),
            new InputOption(
                self::STORE_OPTION,
                's',
                InputOption::VALUE_OPTIONAL,
                "Use specified store id to save the attribute value",
                0
            ),
        ]);
        parent::configure();
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $this->state->setAreaCode('adminhtml');
        $value = $input->getArgument(self::VALUE_ARGUMENT);
        $store = $input->getOption(self::STORE_OPTION);

        if (!$this->attributeManagement->getStatus((int) $store)) {
            $output->writeln("<error>The attribute is not enabled.</error>");
            return Cli::RETURN_FAILURE;
        }

        $collection = $this->productCollectionFactory->create()
            ->setPageSize(100);

        $connection = $collection->getResource()->getConnection();
        $connection->beginTransaction();

        try {
            $lastPage = $collection->getLastPageNumber();
            for ($i = 1; $i <= $lastPage; $i++) {
                $collection->setCurPage($i);
                $collection->load();

                foreach ($collection as $product) {
                    $product->setStoreId($store);
                    $this->attributeManagement->set($product, $value);
                }
                $collection->clear();

                $output->writeln("Processed page/s {$i}/{$lastPage}");
            }

            $connection->commit();
            $output->writeln("Diego attribute value was set as {$value} for all products.");
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $connection->rollBack();
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Cli::RETURN_FAILURE;
        }
    }
}
