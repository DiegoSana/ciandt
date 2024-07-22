<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Vendor\DiegoAttribute\Api\DiegoAttributeManagementInterface;

class DiegoAttribute extends Column
{
    /**
     * @param DiegoAttributeManagementInterface $diegoAttributeManagement
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        protected DiegoAttributeManagementInterface $diegoAttributeManagement,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if ($dataSource['data']['totalRecords'] > 0) {
            foreach ($dataSource['data']['items'] as &$row) {
                $row['diego_attribute'] = $this->diegoAttributeManagement->get($row['sku']);
            }
        }

        return $dataSource;
    }
}
