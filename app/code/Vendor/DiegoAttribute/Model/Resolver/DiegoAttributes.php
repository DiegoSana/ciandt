<?php

declare(strict_types=1);

namespace  Vendor\DiegoAttribute\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Vendor\DiegoAttribute\Api\DiegoAttributeManagementInterface;

class DiegoAttributes implements ResolverInterface
{

    /**
     * @param DiegoAttributeManagementInterface $attributeManagement
     */
    public function __construct(
        protected DiegoAttributeManagementInterface $attributeManagement,
    ) {
    }

    /**
     * @inheritdoc
     * @return array
     * @throws GraphQlInputException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        try {
            $store = $args['store'] ?? null;
            return ['value' => $this->attributeManagement->get($args['sku'], $store)];
        } catch (\Exception $exception) {
            throw new GraphQlInputException(__($exception->getMessage()), $exception);
        }
    }
}
