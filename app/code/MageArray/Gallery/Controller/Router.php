<?php
namespace MageArray\Gallery\Controller;

use MageArray\Gallery\Model\CategoryFactory;
use MageArray\Gallery\Model\ImageFactory;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Router
 * @package MageArray\Gallery\Controller
 */
class Router implements RouterInterface
{

    /**
     * @var ActionFactory
     */
    protected $_actionFactory;
    /**
     * @var ResponseInterface
     */
    protected $_response;
    /**
     * @var
     */
    protected $_categoryFactory;
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var
     */
    protected $_dispatched;
    /**
     * @var \MageArray\Gallery\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var ImageFactory
     */
    protected $_imageFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var
     */
    protected $_url;
    /**
     * @var
     */
    protected $_messageManager;

    protected $_prefix = 2;
    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param UrlInterface $url
     * @param ImageFactory $imageFactory
     * @param CategoryFactory $categoryFactory
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     * @param ResponseInterface $response
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param \MageArray\Gallery\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        UrlInterface $url,
        ImageFactory $imageFactory,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        \MageArray\Gallery\Helper\Data $dataHelper
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_dataHelper = $dataHelper;
        $this->_imageFactory = $imageFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_response = $response;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;

            $postsURLPrefix = $this->_dataHelper->getCategoryUrlPrefix();
            $postsURLSuffix =  $this->_dataHelper->getCategoryUrlSuffix();
            $parts = explode('/', $urlKey);

            $condition = new DataObject([
                'url_key' => $urlKey,
                'continue' => true
            ]);
            $this->_eventManager->dispatch(
                'magearray_gallery_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );
            $urlKey = $condition->getUrlKey();

            if ($condition->getRedirectUrl()) {
                $this->_response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);

                return $this->_actionFactory->create(
                    \Magento\Framework\App\Action\Redirect::Class,
                    ['request' => $request]
                );
            }

            if (!$condition->getContinue()) {
                return null;
            }

            $entities = [
                'author' => [
                    'prefix' => $this->_dataHelper->getCategoryUrlPrefix(),
                    'suffix' => $this->_dataHelper->getCategoryUrlSuffix(),
                    'list_key' => $this->_dataHelper->getPageUrl(),
                    'list_action' => 'index',
                    'controller' => 'index',
                    'action' => 'categorylist',
                    'param' => 'id',
                    'factory' => $this->_imageFactory,
                ]
            ];

            foreach ($entities as $entity => $settings) {
                if ($settings['list_key']) {
                    if ($urlKey == $settings['list_key']) {
                        $request->setModuleName('gallery')
                            ->setControllerName($settings['controller'])
                            ->setActionName($settings['list_action']);
                        $request->setAlias(
                            Url::REWRITE_REQUEST_PATH_ALIAS,
                            $urlKey
                        );

                        $this->_dispatched = true;
                        return $this->_actionFactory->create(
                            \Magento\Framework\App\Action\Forward::Class,
                            ['request' => $request]
                        );
                    }
                }
                $parts = explode('/', $urlKey);
                /*
                if ($settings['suffix']) {
                    $suffix = substr($urlKey, -strlen($settings['suffix']) - 1);

                    if ($suffix != '.' . $settings['suffix']) {
                        continue;
                    }
                    $urlKey = substr(
                        $urlKey,
                        0,
                        -strlen($settings['suffix']) - 1
                    );
                }
                */
                $categoryPath = false;
                if ($settings['prefix']) {
                    $prefix = explode('/', $origUrlKey);

                    if ($parts[0] != $settings['prefix']
                        || count($prefix) != $this->_prefix
                    ) {
                        continue;
                    }
                    if ($parts[0] == $settings['prefix']) {
                        $categoryPath = true;
                    }
                }

                if ($categoryPath) {
                    $prefix = explode('/', $origUrlKey);
                    $urlKeyPart = substr(
                        $prefix[1],
                        0,
                        -strlen($settings['suffix']) - 1
                    );
                    $urlKeyCategory = $prefix[1];
                    $instanceCategory = $this->_categoryFactory->create();
                    $categoryId = $instanceCategory->checkUrlKey($urlKeyCategory);
                    if (!$categoryId) {
                        return null;
                    }
                    $request->setModuleName('gallery')
                        ->setControllerName('category')
                        ->setActionName('categorylist')
                        ->setParam('cat', $categoryId);
                    $request->setAlias(
                        Url::REWRITE_REQUEST_PATH_ALIAS,
                        $origUrlKey
                    );
                    $request->setDispatched(true);
                    $this->_dispatched = true;
                    return $this->_actionFactory->create(
                        \Magento\Framework\App\Action\Forward::Class,
                        ['request' => $request]
                    );
                }
                $instance = $settings['factory']->create();
                $id = $instance->checkUrlKey(
                    $urlKey,
                    $this->_storeManager->getStore()->getId()
                );
                if (!$id) {
                    return null;
                }
                $request->setModuleName('gallery')
                    ->setControllerName('category')
                    ->setActionName('categorylist')
                    ->setParam('id', $id);
                $request->setAlias(
                    Url::REWRITE_REQUEST_PATH_ALIAS,
                    $origUrlKey
                );
                $request->setDispatched(true);
                $this->_dispatched = true;
                return $this->_actionFactory->create(
                    \Magento\Framework\App\Action\Forward::Class,
                    ['request' => $request]
                );
            }
        }
        return null;
    }
}
