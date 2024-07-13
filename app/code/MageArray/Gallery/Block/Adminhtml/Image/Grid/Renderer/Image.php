<?php
namespace MageArray\Gallery\Block\Adminhtml\Image\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Image
 * @package MageArray\Gallery\Block\Adminhtml\Image\Grid\Renderer
 */
class Image extends AbstractRenderer
{

    /**
     * @var
     */
    protected $_imageFactory;
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * Image constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \MageArray\Gallery\Model\ImageFactory $imageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \MageArray\Gallery\Model\ImageFactory $imageFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->_imageFactory = $imageFactory;
        $this->_authorization = $context->getAuthorization();
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $id = $row->getData('image');
        if (!empty($id)) {
            $mediaDirectory = $this->_storeManager->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                );
            $imageUrl = $mediaDirectory . 'gallery/' . $id;
            return '<img src="' .
            $imageUrl .
            '" style="height: 40px; width: 60px;"/>';
        } else {
            $mediaDirectory = $this->_storeManager->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                );
            //$imageUrl = $mediaDirectory . 'gallery/not_found.png';
            $imageUrl = $this->getViewFileUrl('MageArray_Gallery::images/not-found.png');
            return '<img src="' .
            $imageUrl .
            '" style="height: 40px; width: 60px;"/>';
        }
    }
}
