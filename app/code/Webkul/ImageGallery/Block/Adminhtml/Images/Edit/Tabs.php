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
namespace Webkul\ImageGallery\Block\Adminhtml\Images\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Data
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('images_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Images Information'));
    }
}
