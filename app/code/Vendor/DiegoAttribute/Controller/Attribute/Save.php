<?php

namespace Vendor\DiegoAttribute\Controller\Attribute;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Vendor\DiegoAttribute\Api\DiegoAttributeManagementInterface;

class Save implements HttpPostActionInterface
{

    /**
     * @param RedirectFactory $redirectFactory
     * @param UrlInterface $urlBuilder
     * @param RedirectInterface $redirect
     * @param ManagerInterface $messageManager
     * @param DiegoAttributeManagementInterface $attributeManagement
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        protected RedirectFactory $redirectFactory,
        protected UrlInterface $urlBuilder,
        protected RedirectInterface $redirect,
        protected ManagerInterface $messageManager,
        protected DiegoAttributeManagementInterface $attributeManagement,
        protected StoreManagerInterface $storeManager,
        protected RequestInterface $request,
        protected ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Save diego attribute value
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        try {
            $sku = $this->request->getParam('sku');
            $value = $this->request->getParam('diego_attribute');
            $storeId = $this->storeManager->getStore()->getId();
            $product = $this->productRepository->get($sku)->setStoreId($storeId);
            $this->attributeManagement->set($product, $value);
            $this->messageManager->addSuccessMessage(__('Diego attribute saved.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Diego attribute save failed.'));
        }

        $url = $this->redirect->getRefererUrl();
        $resultRedirect = $this->redirectFactory->create();
        $resultRedirect->setUrl($url);

        return $resultRedirect;
    }
}
