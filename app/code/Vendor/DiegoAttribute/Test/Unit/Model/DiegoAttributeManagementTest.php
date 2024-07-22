<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Test\Unit\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Vendor\DiegoAttribute\Model\DiegoAttributeManagement;
use Magento\Store\Model\Store;

class DiegoAttributeManagementTest extends TestCase
{
    /**
     * @var DiegoAttributeManagement $object
     */
    protected $object;

    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->getMockForAbstractClass(ProductRepositoryInterface::class);
        $this->configMock = $this->getMockForAbstractClass(ConfigInterface::class);
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)->getMock();

        $this->objectManager = new ObjectManager($this);
        $this->object = $this->objectManager->getObject(
            DiegoAttributeManagement::class,
            [
                'productRepository' => $this->productRepositoryMock,
                'config' => $this->configMock,
                'scopeConfig' => $this->scopeConfigMock,
            ]
        );
    }

    public function testGet()
    {
        $productAttributeMock = $this->createMock(AttributeInterface::class);
        $productMock = $this->createMock(Product::class);

        $productMock->expects($this->once())->method('getCustomAttribute')->with('diego_attribute')
            ->willReturn($productAttributeMock);
        $productAttributeMock->expects($this::once())->method('getValue')->willReturn('testValue');

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with('testSku', false, Store::DEFAULT_STORE_ID)
            ->willReturn($productMock);

        $this->assertSame(
            'testValue',
            $this->object->get('testSku', Store::DEFAULT_STORE_ID)
        );
    }

    public function testGetNoSuchEntityException()
    {
        $this->productRepositoryMock->expects($this->once())->method('get')
            ->willThrowException(new NoSuchEntityException());

        $this->expectException(NoSuchEntityException::class);

        $this->object->get('testSku', Store::DEFAULT_STORE_ID);
    }

    public function testGetAttributeNotFoundException()
    {
        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCustomAttribute'])
            ->getMock();
        $this->productRepositoryMock->expects($this->once())->method('get')
            ->with('testSku', false, Store::DEFAULT_STORE_ID)->willReturn($productMock);
        $productMock->expects($this->once())->method('getCustomAttribute')->willReturn(null);

        $this->assertEquals('', $this->object->get('testSku', Store::DEFAULT_STORE_ID));
    }

    public function testSetCouldNotSaveException()
    {
        $productMock = $this->createMock(Product::class);
        $this->productRepositoryMock->expects($this->once())->method('save')->with($productMock)
            ->willThrowException(new CouldNotSaveException(__('Some error.')));

        $this->expectException(CouldNotSaveException::class);

        $this->object->set($productMock, 'testValue');
    }
}
