<?php
namespace MageArray\Gallery\Block\Links;

/**
 * Class Link
 * @package MageArray\Gallery\Block\Links
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{

    /**
     * @var \MageArray\Gallery\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Link constructor.
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
     * @return string
     */
    public function getHref()
    {
        if ($this->_dataHelper->isEnabled() == 1) {
            $pageUrl = $this->_dataHelper->getPageUrl();
            return $this->getUrl($pageUrl);
        }
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        if ($this->_dataHelper->isEnabled() == 1) {
            return $this->_dataHelper->getLinkTitle();
        }
    }
}
