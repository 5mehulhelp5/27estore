<?php
namespace MageArray\Gallery\Block\Adminhtml\Image\Form\Renderer;

use MageArray\Gallery\Helper\Data as GalleryImage;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\Image as ImageField;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;

/**
 * Class Customfield
 * @package MageArray\Gallery\Block\Adminhtml\Image\Form\Renderer
 */
class Customfield extends ImageField
{

    /**
     * @var GalleryImage
     */
    protected $imageModel;

    /**
     * Customfield constructor.
     * @param GalleryImage $imageModel
     * @param ElementFactory $factoryElement
     * @param ElementCollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        GalleryImage $imageModel,
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        $data = []
    ) {
        $this->imageModel = $imageModel;
        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $urlBuilder,
            $data
        );
    }

    /**
     * @return bool|string
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->imageModel
                    ->getBaseUrlMedia() .
                'gallery/' .
                $this->getValue();
        }
        return $url;
    }
}
