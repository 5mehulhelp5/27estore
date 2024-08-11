<?php
namespace WeltPixel\Quickview\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class QuickviewSkuObserver implements ObserverInterface
{

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getEvent()->getRequest();

        $sku = $request->getParam('sku', false);

        if ($sku) {
            try {
                $product = $this->productRepository->get($sku);
                $request->setParam('id', $product->getId());
            } catch (NoSuchEntityException $e) {
            }
        }

        return $this;
    }
}
