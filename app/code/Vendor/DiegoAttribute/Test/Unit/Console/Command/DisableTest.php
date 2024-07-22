<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Test\Unit\Console\Command;

use Magento\Framework\Console\Cli;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vendor\DiegoAttribute\Console\Command\Disable;
use Vendor\DiegoAttribute\Model\DiegoAttributeManagement;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DisableTest extends TestCase
{
    /**
     * @var Disable object
     */
    protected $object;

    protected function setUp(): void
    {
        $this->attributeManagementMock = $this->createMock(DiegoAttributeManagement::class);
        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(
            Disable::class,
            ['attributeManagement' => $this->attributeManagementMock]
        );
    }

    public function testExecute()
    {
        $outputMock = $this->getMockBuilder(OutputInterface::class)->getMock();
        $outputMock->expects($this->once())->method('writeln')
            ->with("Diego attribute value was disabled.");

        $inputMock = $this->getMockBuilder(InputInterface::class)->getMockForAbstractClass();
        $inputMock->expects($this->once())
            ->method('getOption')
            ->with('store')
            ->willReturn(1);

        $this->attributeManagementMock->expects($this->once())->method('setStatus');

        $this->assertEquals(
            Cli::RETURN_SUCCESS,
            $this->object->run($inputMock, $outputMock)
        );
    }

    public function testExecuteWithException()
    {
        $errorMessage = 'Exception message';

        $outputMock = $this->getMockBuilder(OutputInterface::class)->getMock();
        $outputMock->expects($this->once())->method('writeln')
            ->with("<error>{$errorMessage}</error>");

        $inputMock = $this->getMockBuilder(InputInterface::class)->getMockForAbstractClass();
        $inputMock->expects($this->once())
            ->method('getOption')
            ->with('store')
            ->willReturn(1);

        $this->attributeManagementMock->expects($this->once())->method('setStatus')
            ->willThrowException(new \Exception($errorMessage));

        $this->assertEquals(
            Cli::RETURN_FAILURE,
            $this->object->run($inputMock, $outputMock)
        );
    }
}
