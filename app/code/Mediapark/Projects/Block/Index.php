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

class Index extends Template
{
    protected $_projectFactory;

    protected $_roomFactory;

    protected $_itemFactory;

    protected $_productFactory;

    protected $_objectManager;

    protected $_isRequestPrice;

    public function __construct(
        Context $context,
        \Mediapark\Projects\Model\ProjectFactory $projectFactory,
        \Mediapark\Projects\Model\RoomFactory $roomFactory,
        \Mediapark\Projects\Model\ItemFactory $itemFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
    ) {
        parent::__construct($context);
        $this->_projectFactory = $projectFactory;
        $this->_roomFactory = $roomFactory;
        $this->_itemFactory = $itemFactory;
        $this->_productFactory = $productFactory;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_isRequestPrice = false;
    }

    public function getIsRequestPrice()
    {
        if (isset($this->_isRequestPrice) && $this->_isRequestPrice) {
            return true;
        }

        return false;
    }

    public function getUserId()
    {
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if ($customerSession->isLoggedIn()) {
            $id = $customerSession->getCustomerId();

            return $id;
        }

        return -1;
    }

    public function getUserProjects()
    {
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if ($customerSession->isLoggedIn()) {
            $id = $customerSession->getCustomerId();
            $productCollection = $this->_projectFactory
                ->create()->getCollection()->addFieldToFilter('project_user_id', $id);

            return $productCollection;
        }

        return [];
    }

    public function getUserProjectRoomsList()
    {
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if ($customerSession->isLoggedIn()) {
            $id = $customerSession->getCustomerId();
            $productCollection = $this->_projectFactory
                ->create()->getCollection()->addFieldToFilter('project_user_id', $id);
            $list = [];
            foreach ($productCollection as $item) {
                $list[$item->getData()['project_id']] = $item->getData()['project_name'];
            }

            return $list;
        }

        return [];
    }

    public function getUserProjectsList()
    {
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if ($customerSession->isLoggedIn()) {
            $id = $customerSession->getCustomerId();
            $productCollection = $this->_projectFactory
                ->create()->getCollection()->addFieldToFilter('project_user_id', $id);
            $list = [];
            foreach ($productCollection as $item) {
                $list[$item->getData()['project_id']] = $item->getData()['project_name'];
            }

            return $list;
        }

        return [];
    }

    public function getProjectRooms($roomIds)
    {
        if (!empty($roomIds)) {
            if (is_string($roomIds)) {
                $roomIds = json_decode($roomIds);
            }
            $roomCollection = $this->_roomFactory
                ->create()->getCollection()->addFieldToFilter('room_id', $roomIds);

            return $roomCollection;
        }

        return [];
    }

    public function getRoomItems($itemIds)
    {
        if (!empty($itemIds)) {
            if (is_string($itemIds)) {
                $itemIds = json_decode($itemIds);
            }
            $itemCollection = [];
            foreach ($itemIds as $item) {
                $itemObject = $this->getObject($item, "Item");
                $productObject =
                    $this->_objectManager->create('Magento\Catalog\Model\Product')->load($itemObject->getProductId());
                $itemCollection [] = ["item" => $itemObject, "product" => $productObject];
            }

            return $itemCollection;
        }

        return [];
    }

    public function getNumOfItems($projectRooms)
    {
        $numOfItems = 0;
        $rooms = $this->getProjectRooms($projectRooms);
        if (!empty(json_decode($projectRooms))) {
            foreach ($rooms as $room) {
                $items = $this->getRoomItems($room->getData()['room_items']);
                foreach ($items as $key => $item) {
                    $qty = $item["item"]->getItemQty();
                    $numOfItems += $qty;
                }
            }
        }

        return $numOfItems;
    }

    public function getTotalPrice($projectRooms)
    {
        $totalPrice = 0.0;
        //$totalPrice = "";
        $rooms = $this->getProjectRooms($projectRooms);
        if (!empty(json_decode($projectRooms))) {

            foreach ($rooms as $room) {
                $items = $this->getRoomItems($room->getData()['room_items']);
                foreach ($items as $key => $item) {
                    $totalPrice += $this->getProductPrice($item) * $item["item"]->getData()['item_qty'];
                }
            }
        }

        return ($totalPrice == 0) ? "-" : $totalPrice;
    }

    public function getProductPrice($item)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $brandRepository = $objectManager->create('TemplateMonster\ShopByBrand\Api\BrandRepositoryInterface');
        //check is this product has public price
        $brandId = $item['product']->getBrandId();
        $isShowPrice = true;
        if (isset($brandId) && !empty($brandId)) {
            $brand = $brandRepository->getById($brandId);
        }
        if (isset($brand) && !is_bool($brand)) {
            $isShowPrice = $brand->getIsShowPrice();
        }
        if ($isShowPrice) {
            $finalPrice = $item['product']->getPriceInfo()->getPrice('final_price')->getValue()
                * $item["item"]->getItemQty();
            // consider sales, etc
            $specialPrice = floatval($item['product']->getFinalPrice());

            return $specialPrice;
        } else {
            $this->_isRequestPrice = true;
        }

        return 0.0;
    }

    /**
     * @param $projectId
     * @return float|int|string
     */
    public function getTotalPriceByProjectId($projectId)
    {
        $project = $this->getObject($projectId, "Project");

        return $this->getTotalPrice($project->getProjectRooms());
    }

    /**
     * @param $id
     * @return string
     */
    public function getLinkToProject($id)
    {
        $int_value = ctype_digit($id) ? intval($id) : null;
        if ($int_value !== null) {
            /** @var \Magento\Framework\UrlInterface $urlInterface */
            $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
            $url = $urlInterface->getUrl();
            if (isset($url) && !empty($url)) {
                return $url . 'projects/customer/projects?id=' . $id;
            }

            return "Err_Getting_Url";
        }

        return "Wrong_Id=" . strval($id);
    }

    /**
     * @return bool
     */
    public function isUserLoggedIn()
    {
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if ($customerSession->isLoggedIn()) {
            return true;
        }

        return false;
    }

    /**
     * @param $projectId
     */
    public function deleteProject($projectId)
    {
        $project = $this->getObject($projectId, "Project");
        $rooms = json_decode($project->getData()['project_rooms']);
        foreach ($rooms as $roomId) {
            $this->deleteRoom($roomId);
        }
        $project->delete();
    }

    /**
     * @param $roomId
     */
    public function deleteRoom($roomId)
    {
        $room = $this->getObject($roomId, "Room");
        $items = json_decode($room->getData()['room_items']);
        foreach ($items as $itemId) {
            $this->deleteItem($itemId);
        }
        $room->delete();
    }

    /**
     * @param $itemId
     */
    public function deleteItem($itemId)
    {
        $this->getObject($itemId, 'Item')->delete();
    }

    /**
     * @param $id
     * @param $strType
     * @return mixed
     */
    public function getObject($id, $strType)
    {
        return $this->_objectManager->create('Mediapark\Projects\Model\\' . $strType)->load($id);
    }
}