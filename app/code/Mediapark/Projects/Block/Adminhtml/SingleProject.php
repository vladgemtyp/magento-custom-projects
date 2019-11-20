<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 */

namespace Mediapark\Projects\Block\Adminhtml;

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

    protected $_project_id;

    protected $_project;

    protected $_customer;

    protected $productRepository;

    protected $configurable;

    public function __construct(
        Context $context,
        RequestInterface $request,
        \Mediapark\Projects\Model\ProjectFactory $projectFactory,
        \Mediapark\Projects\Model\RoomFactory $roomFactory,
        \Mediapark\Projects\Model\ItemFactory $itemFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_projectFactory = $projectFactory;
        $this->_roomFactory = $roomFactory;
        $this->_itemFactory = $itemFactory;
        $this->_messageManager = $messageManager;
        $this->productRepository = $productRepository;
        $this->configurable = $configurable;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getProjectIdFromUrl()
    {
        $this->_project_id = $this->_request->getParam('id');

        return $this->_project_id;
    }

    /**
     * @return mixed
     */
    protected function getProjectId()
    {
        if (!empty($this->_project_id)) {
            $this->_project_id = $this->getProjectIdFromUrl();
        }

        return $this->_project_id;
    }

    /**
     * @return mixed
     */
    protected function getProject()
    {
        if (!isset($this->_project)) {
            $this->_project =
                $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($this->getProjectId());
        }

        return $this->_project;
    }

    /**
     * @return mixed
     */
    public function getProjectData()
    {
        return $this->getProject()->getData();
    }

    /**
     * @param $project
     * @return array
     */
    protected function getProjectRooms($project)
    {
        $roomIdList = json_decode($project->getData()['project_rooms']);
        $roomList = [];
        foreach ($roomIdList as $roomId) {
            $roomList[$roomId] = $this->_objectManager->create('Mediapark\Projects\Model\Room')->load($roomId);
        }

        return $roomList;
    }

    /**
     * @return mixed
     */
    public function getCustomerData()
    {
        if (!isset($this->_customer)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')
                ->create()
                ->load($this->getProjectData()['project_user_id']);
        }

        return $this->_customer;
    }

    /**
     * @return array
     */
    public function getItemList()
    {
        $roomList = $this->getProjectRooms($this->getProject());
        $itemList = [];
        foreach ($roomList as $room) {
            $itemIdList = json_decode($room->getData()['room_items']);
            foreach ($itemIdList as $itemId) {
                $itemList [$itemId] = $this->_objectManager->create('Mediapark\Projects\Model\Item')->load($itemId);
            }
        }

        return $itemList;
    }

    /**
     * @param $item
     * @return bool
     */
    public function getProductFromItem($item)
    {
        $productId = $item->getData()['product_id'];
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productR = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
            $product = $productR->getById($productId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $product = false;
        }

        return $product;
    }

    /**
     * @param $item
     * @return bool|mixed|string
     */
    public function getItemName($item)
    {
        try {
            $product = $this->getProductFromItem($item);
            if ($product) {
                $res = $product->getData()['name'];
            } else {
                $res = false;
            }
        } catch (\Exception $ex) {
            return "exception";
        }
        if (!is_bool($res)) {
            if (isset($res) && !empty($res)) {
                return $res;
            }
        }

        return "Product was removed";
    }

    /**
     * @param $item
     * @return float|string
     */
    public function getItemPrice($item)
    {
        if ($this->getProductFromItem($item)) {
            $specialPrice = floatval($this->getProductFromItem($item)->getFinalPrice());
        } else {
            $specialPrice = '-';
        }

        return $specialPrice;
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemQty($item)
    {
        if ($this->getProductFromItem($item)) {
            return $item->getData()['item_qty'];
        } else {
            return '-';
        }
    }

    /**
     * @param $item
     * @return array
     */
    public function getItemOptions($item)
    {
        $result = [];
        if ($item) {
            $availableOptionsForProduct = $this->getProductOptionsArray($item);
            $params = json_decode($item->getData()['params'], true);
            if (array_key_exists("options", $params)) {
                foreach ($params['options'] as $key => $option) {
                    if (isset($availableOptionsForProduct[$key - 1])) {
                        //if we have such option
                        $tmp1 = $availableOptionsForProduct[$key - 1][intval($option)];
                        $result[$tmp1] = $availableOptionsForProduct[$key - 1]["title"];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param $item
     * @return string
     */
    public function getProductSkuFromItem($item)
    {
        if ($item) {
            //get product id
            $productId = $item->getData()['product_id'];
            // get product
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $_objectManager->get('\Magento\Catalog\Model\Product')->load($productId);
            $itemParams = json_decode($item->getParams(), true);
            if (isset($itemParams['super_attribute'])) {
                $childId = $this->getChildFromProductAttribute($productId, $itemParams['super_attribute']);

                return $childId;
            }

            return $product->getSku();
        }

        return '';
    }

    /**
     * @param $configId
     * @param $superAttribute
     * @return mixed
     */
    public function getChildFromProductAttribute($configId, $superAttribute)
    {
        $_configProduct = $this->productRepository->getById($configId);
        $usedChild = $this->configurable->getProductByAttributes($superAttribute, $_configProduct);

        return $usedChild->getSku();
    }

    /**
     * @param $item
     * @return array
     */
    public function getProductOptionsArray($item)
    {
        $productOptionArray = [];
        if ($item) {
            $itemData = $item->getData();
            //get params
            $itemDataParams = json_decode($item->getData()['params']);
            //if we have options in item - product have option params
            if (array_key_exists("options", $itemDataParams)) {
                //get product
                $product = $this->_objectManager->get('\Magento\Catalog\Model\Product')->load($itemData['product_id']);
                //take this array of options
                $productOptions = $product->getData()["options"];
                foreach ($productOptions as $key => $productOption) {
                    // get type of option
                    $values = $productOption->getValues();
                    // example wooden types
                    $productOptionArray[$key] = ["title" => $productOption->getData()['title']];
                    foreach ($values as $value) {

                        /* PRODUCT OPTIONS ARRAY*/
                        $productOptionArray[$key][$value->getData()['option_type_id']] = $value->getData()['title'];
                        /*this values uses near sku (look order #000000003 in admin magento)
                        * return $productOptionArray;
                        *
                            [0] => [
                                 [title] => Wood Types,
                                 [1] => Oak,
                                 [2] => Pine,
                                 [3] => Ash,
                                 [4] => Maple,
                            ],
                            [1] =>[
                                 [title] => Finishes,
                                 [5] => Brown Finish,
                                 [6] => Cherry Finish,
                                 [7] => Distressed,
                                 [8] => White Finish,
                            ]
                          */
                    }
                }
            }
        }

        return $productOptionArray;
    }
}