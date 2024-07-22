<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Test\Unit\Controller\Attribute;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Vendor\DiegoAttribute\Controller\Attribute\Save;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Vendor\DiegoAttribute\Model\DiegoAttributeManagement;
use Magento\Framework\Controller\Result\Redirect;

class SaveTest extends TestCase
{

    /**
     * @var Save object
     */
    protected $object;

    protected function setUp(): void
    {
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->redirectFactoryMock = $this->createPartialMock(
            RedirectFactory::class,
            ['create']
        );
        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)->getMock();
        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $this->attributeManagementMock = $this->createMock(DiegoAttributeManagement::class);
        $this->redirectMock = $this->getMockBuilder(RedirectInterface::class)->getMock();
        $this->productRepositoryMock = $this->getMockForAbstractClass(ProductRepositoryInterface::class);

        $this->redirectMock->expects($this->once())->method('getRefererUrl')->willReturn('testUrl');
        $redirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->redirectFactoryMock->expects($this->once())->method('create')->willReturn($redirectMock);
        $redirectMock->expects($this->once())->method('setUrl')->with('testUrl')->willReturn($redirectMock);
        $this->requestMock->expects($this->any())->method('getParam')->willReturnMap([
            ['sku', null, 'testSku'],
            ['diego_attribute', null, 'testValue']
        ]);

        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(
            Save::class,
            [
                'redirectFactory' => $this->redirectFactoryMock,
                'urlBuilder' => $this->urlBuilderMock,
                'redirect' => $this->redirectMock,
                'messageManager' => $this->messageManagerMock,
                'attributeManagement' => $this->attributeManagementMock,
                'storeManager' => $this->storeManagerMock,
                'request' => $this->requestMock,
                'productRepository' => $this->productRepositoryMock,
            ]
        );
    }

    public function testExecuteNoProductException()
    {
        $storeMock = $this->getMockBuilder(Store::class)
        ->disableOriginalConstructor()
        ->getMock();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(Store::DEFAULT_STORE_ID);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->willThrowException(new NoSuchEntityException());

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with("No such entity.");

        $this->object->execute();
    }

    public function testExecuteNoStoreException()
    {
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with("No such entity.");

        $this->object->execute();
    }

    public function testExecuteCouldNotSaveException()
    {
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(Store::DEFAULT_STORE_ID);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStoreId'])
            ->getMock();
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($productMock);

        $this->attributeManagementMock->expects($this->once())
            ->method('set')
            ->willThrowException(new CouldNotSaveException(__('Some error')));

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with("Diego attribute save failed.");

        $this->object->execute();
    }

    public function testExecuteSuccess()
    {
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(Store::DEFAULT_STORE_ID);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStoreId'])
            ->getMock();
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($productMock);

        $this->attributeManagementMock->expects($this->once())
            ->method('set')
            ->with($productMock, 'testValue');

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with("Diego attribute saved.");

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->object->execute();
    }
}
