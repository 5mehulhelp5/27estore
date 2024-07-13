<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_ImageGallery
 * @author    Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\ImageGallery\Model;
 
use Webkul\ImageGallery\Model\ResourceModel\Images\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
 
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    public $_storeManager;
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $imageCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $imageCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $imageCollectionFactory->create();
    
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    /**
     * Get GetData
     *
     * @return $this
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $_data = $item->getData();
            $item->load($item->getId());
            if (isset($_data['image'])) {
                $image = [];
                $image[0]['name'] = $item->getImagePath();
                $image[0]['url'] = $this->getMediaUrl().$item->getImage();
                $_data['image'] = $image;
            }
            $item->setData($_data);
            $this->_loadedData[$item->getId()] = $_data;
        }
        return $this->_loadedData;
    }
    /**
     * Get MediaUrl
     *
     * @return $this
     */
    public function getMediaUrl()
    {
        $type = UrlInterface::URL_TYPE_MEDIA;
        return $this->_storeManager->getStore()->getBaseUrl($type);
    }
}
