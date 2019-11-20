<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 */

namespace Mediapark\Projects\Model;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Result\PageFactory;
use Mediapark\Projects\Api\ProjectsInterface;

class ProjectsApi implements ProjectsInterface
{
    protected $_projectFactory;

    protected $_roomFactory;

    protected $_itemFactory;

    protected $_productFactory;

    protected $_objectManager;

    protected $formKey;

    protected $cart;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ProjectFactory $projectFactory,
        RoomFactory $roomFactory,
        ItemFactory $itemFactory,
        FormKey $formKey,
        Cart $cart
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_itemFactory = $itemFactory;
        $this->_projectFactory = $projectFactory;
        $this->_roomFactory = $roomFactory;
        $this->_itemFactory = $itemFactory;
        $this->_objectManager = ObjectManager::getInstance();
        $this->formKey = $formKey;
        $this->cart = $cart;
    }

    /**
     * Changes status for project
     *
     * @api
     * @param int $id project id.
     * @return string "ok" if success.
     */
    public function setProjectPendingStatus($id)
    {
        $project = $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($id);
        $project->setStatus("Pending");
        $project->save();

        return "ok";
    }

    /**
     * Changes status for project with value from query
     *
     * @api
     * @param string $id project id.
     * @param string $status.
     * @return string "{project_id: 'ID', status: 'STATUS'}"
     */
    public function setProjectStatus($id, $status)
    {
        $project = $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($id);
        $status = ucfirst($status);
        $project->setStatus($status);
        $project->save();

        return "{'project_id': " . $id . ", 'status': " . $status . "}";
    }

    /**
     * Returns project Rooms by project id
     *
     * @api
     * @param int $id Users name.
     * @return string json with rooms.
     */
    public function projectRooms($id)
    {
        //get project instance
        $projectRooms = $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($id);
        //get array of all rooms based on jsoned array from project instance
        $roomsArray = json_decode($projectRooms->getData()['project_rooms']);
        $resultArray = [];
        if (!empty($roomsArray)) {
            $roomCollection = $this->_roomFactory
                ->create()->getCollection()->addFieldToFilter('room_id', $roomsArray);
            foreach ($roomCollection as $roomId) {
                $this->log("entered foreach");
                $resultArray[$roomId->getData()['room_id']] = $roomId->getData()['room_name'];
            }
        }
        $json = json_encode($resultArray);

        return $json;
    }

    /**
     * Returns projects by user id
     *
     * @api
     * @param int $userId Users name.
     * @return string json with projects.
     */
    public function userProjects($userId)
    {

        $productCollection = $this->_projectFactory
            ->create()->getCollection()->addFieldToFilter('project_user_id', $userId);
        if (isset($productCollection)) {
            $list = [];
            foreach ($productCollection as $item) {
                $list[$item->getData()['project_id']] = $item->getData()['project_name'];
            }

            return json_encode($list);
        }

        return json_encode([]);
    }

    /**
     * Delete Project by id
     *
     * @api
     * @param int $projectId Users name.
     * @return string Greeting message with users name.
     */
    public function deleteProject($projectId)
    {
        $project = $this->getObject($projectId, "Project");
        $rooms = json_decode($project->getData()['project_rooms']);
        foreach ($rooms as $roomId) {
            $this->deleteRoom($roomId);
        }
        $project->delete();

        return "Success";
    }

    /**
     * Delete room from project by id
     *
     * @api
     * @param int $roomId.
     */
    public function deleteRoom($roomId)
    {
        $room = $this->getObject($roomId, "Room");
        $items = json_decode($room->getData()['room_items']);
        foreach ($items as $itemId) {
            $this->deleteItem($itemId);
        }
        $project = $this->getObject($room->getData()['project_id'], "Project");
        $project->removeRoom($roomId);
        $room->delete();
    }

    /**
     * Delete item from project by id
     *
     * @api
     * @param int $itemId
     */
    public function deleteItem($itemId)
    {
        $item = $this->getObject($itemId, 'Item');
        $room = $this->getObject($item->getRoomId(), "Room");
        $room->removeItem($itemId);
        $item->delete();
    }

    public function getObject($id, $strType)
    {
        return $this->_objectManager->create('Mediapark\Projects\Model\\' . $strType)->load($id);
    }

    /**
     * Add product by id to user's cart
     *
     * @api
     * @param int $id Users name.
     * @return string "success" or "User is not logged in"
     */
    public function addSingleProductToCart($id)
    {
        /** @var Session $customerSession */
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        $res = "";
        if ($customerSession->isLoggedIn()) {
            $asd = $customerSession->getCustomerData();
            $res = "1";
            //return "success" . $id;
            $itemObject = $this->getObject($id, "Item");
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                ->load($itemObject->getData()['product_id']);
            $params = json_decode($itemObject->getData()['params'], true);
            $params['form_key'] = $this->formKey->getFormKey();
            $this->cart->addProduct($product, $params);
            $this->cart->save();

            return "success";
        }

        return "user_is_not_logged_in";
    }
}