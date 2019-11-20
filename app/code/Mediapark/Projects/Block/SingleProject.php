<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 */

namespace Mediapark\Projects\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;

class SingleProject extends Template
{
    protected $_objectManager;

    protected $_projectFactory;

    protected $_roomFactory;

    protected $_itemFactory;

    protected $_messageManager;

    public function __construct(
        Context $context,
        RequestInterface $request,
        \Mediapark\Projects\Model\ProjectFactory $projectFactory,
        \Mediapark\Projects\Model\RoomFactory $roomFactory,
        \Mediapark\Projects\Model\ItemFactory $itemFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_projectFactory = $projectFactory;
        $this->_roomFactory = $roomFactory;
        $this->_itemFactory = $itemFactory;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getProjectIdFromUrl()
    {
        return $this->_request->getParam('id');
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getProjectbyId($projectId)
    {
        $product = $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($projectId);

        return $product->getData();
    }

    /**
     * @param $roomIds
     * @return array
     */
    public function getProjectRooms($roomIds)
    {
        if (!empty($roomIds)) {
            if (is_string($roomIds)) {
                $roomIds = json_decode($roomIds);
            }
            if (!empty($roomIds)) {
                $roomCollection = $this->_roomFactory
                    ->create()->getCollection()->addFieldToFilter('room_id', $roomIds);

                return $roomCollection;
            }
        }

        return [];
    }

    /**
     * @param $itemIds
     * @return array
     */
    public function getRoomItems($itemIds)
    {
        if (!empty($itemIds)) {
            if (is_string($itemIds)) {
                $itemIds = json_decode($itemIds);
            }
            $itemCollection = [];
            foreach ($itemIds as $item) {
                $itemObject = $this->_objectManager->create('Mediapark\Projects\Model\Item')->load($item);
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                    ->load($itemObject->getData()['product_id']);
                $itemCollection [$item] = ["item" => $itemObject, "product" => $product];
            }

            return $itemCollection;
        }

        return [];
    }

    /**
     * @param $productData
     * @return string
     */
    public function getName($productData)
    {
        if (array_key_exists('name', $productData)) {
            $name = $productData['name'];
        } else {
            $name = 'Product doesn\'t exist';
        }

        return $name;
    }

    /**
     * @param $productData
     * @return string
     */
    public function getLinkToProduct($productData)
    {
        /** @var \Magento\Framework\UrlInterface $urlInterface */
        $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
        $url = $urlInterface->getUrl();
        if (array_key_exists('url_key', $productData)) {
            $title = str_replace(' ', '-', strtolower($productData['url_key']));
        } else if (array_key_exists('name', $productData)) {
            $title = str_replace(' ', '-', strtolower($productData['name']));
        } else {
            //$this->myLog(json_encode($productData));
            return $url;
        }

        return $url . $title . ".html";
    }
}