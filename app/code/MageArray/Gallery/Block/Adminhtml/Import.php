<?php
namespace MageArray\Gallery\Block\Adminhtml;

/**
 * Class Import
 * @package MageArray\Gallery\Block\Adminhtml
 */
class Import extends \Magento\Backend\Block\Template
{

    /**
     * @return string
     */
    public function getImageMediaUrl()
    {
        return $this->getViewFileUrl('MageArray_Gallery::image.csv');
    }

    /**
     * @return string
     */
    public function getCategoryMediaUrl()
    {
        return $this->getViewFileUrl('MageArray_Gallery::category.csv');
    }
}
