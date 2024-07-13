<?php
namespace MageArray\Gallery\Block\Image;

/**
 * Class ImageDescription
 * @package MageArray\Gallery\Block\Image
 */
class ImageDescription extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \MageArray\Gallery\Helper\Data
     */
    protected $_dataHelper;

    /**
     * ImageDescription constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageArray\Gallery\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\Gallery\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @return mixed
     */
    public function getPageDescription()
    {
        return $this->_dataHelper->getPageDescription();
    }
}
