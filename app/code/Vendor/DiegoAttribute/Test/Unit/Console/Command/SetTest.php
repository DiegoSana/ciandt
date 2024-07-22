<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Test\Unit\Console\Command;

use Magento\Catalog\Ui\DataProvider\Product\ProductCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vendor\DiegoAttribute\Console\Command\Set;
use Vendor\DiegoAttribute\Model\DiegoAttributeManagement;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class SetTest extends TestCase
{
    /**
     * @var Set object
     */
    protected $object;

    protected function setUp(): void
    {
        $this->stateMock = $this->createMock(State::class);
        $this->attributeManagementMock = $this->createMock(DiegoAttributeManagement::class);
        $this->productCollectionFactoryMock = $this->createMock(ProductCollectionFactory::class);

        $this->stateMock->expects($this->once())->method('setAreaCode')->with('adminhtml');

        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(
            Set::class,
            [
                'attributeManagement' => $this->attributeManagementMock,
                'state' => $this->stateMock,
                'productCollectionFactory' => $this->productCollectionFactoryMock
            ]
        );
    }

    public function testExecuteAttributeDisabled()
    {
        $outputMock = $this->getMockBuilder(OutputInterface::class)->getMock();
        $inputMock = $this->getInputMock();

        $this->attributeManagementMock->expects($this->once())->method('getStatus')->with(1)->willReturn(false);

        $outputMock->expects($this->once())->method('writeln')
            ->with("<error>The attribute is not enabled.</error>");

        $this->assertEquals(
            Cli::RETURN_FAILURE,
            $this->object->run($inputMock, $outputMock)
        );
    }

    protected function getInputMock()
    {
        $inputMock = $this->getMockBuilder(InputInterface::class)->getMockForAbstractClass();
        $inputMock->expects($this->once())
            ->method('getOption')
            ->with("store")
            ->willReturn(1);

        $inputMock->expects($this->once())
            ->method('getArgument')
            ->with("value")
            ->willReturn('testValue');

        return $inputMock;
    }
}
