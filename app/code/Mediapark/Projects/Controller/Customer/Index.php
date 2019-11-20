<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 */

namespace Mediapark\Projects\Controller\Customer;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $_itemFactory;

    protected $date;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Mediapark\Projects\Model\ItemFactory $itemFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_itemFactory = $itemFactory;
        $this->date = $date;

        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if (!$customerSession->isLoggedIn()) {
            //if customer didn't log in -> redirect him to log in page
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create()->setPath('customer/account/login');

            return $resultRedirect;
        }
        // simple load customer's project list
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * @param $projectName
     * @param $userId
     * @return mixed
     */
    public function createProject($projectName, $userId)
    {
        $project = $this->_objectManager->create('Mediapark\Projects\Model\Project');
        $project->setProjectName($projectName);
        $project->setProjectUserId($userId);
        $project->setCreatedAt($this->date->gmtDate());
        $project->setProjectRooms("[]");
        $project->setStatus("Created");
        $project->save();

        return $project;
    }

    /**
     * @param $projectId
     * @param $roomId
     * @return \Mediapark\Projects\Model\Project
     */
    public function updateProject($projectId, $roomId)
    {
        /** @var \Mediapark\Projects\Model\Project $project */
        $project = $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($projectId);
        $project->appendRooms($roomId);
        $project->save();

        return $project;
    }

    /**
     * @param $post
     */
    public function updateRoom($post)
    {
        if (array_key_exists('product', $post)) {
            /** @var \Mediapark\Projects\Model\Room $room */
            $room = $this->_objectManager->create('Mediapark\Projects\Model\Room')->load($post['projectroomsId']);
            $item = $this->createItem($post);
            $room->appendItems($item->getItemId());
        }
    }

    /**
     * @param $params
     * @return \Mediapark\Projects\Model\Item
     */
    public function createItem($params)
    {
        /** @var \Mediapark\Projects\Model\Item $item */
        $item = $this->_objectManager->create('Mediapark\Projects\Model\Item');
        $item->setProductId($params['product']);
        // if we have no qty - set 1, it's possible when we add product with hidden price
        $params['qty'] = array_key_exists('qty', $params) ? $params['qty'] : 1;
        $item->setItemQty($params['qty']);
        $item->setParams(json_encode($params));
        $item->setRoomId($params['projectroomsId']);
        $item->setProjectId($params['projectId']);
        if (array_key_exists('item_comment', $params)) {
            $item->setComment($params['item_comment']);
        }
        $item->save();

        return $item;
    }

    /**
     * @param $params
     * @return \Mediapark\Projects\Model\Room
     */
    public function createRoom($params)
    {
        /** @var \Mediapark\Projects\Model\Room $room */
        $room = $this->_objectManager->create('Mediapark\Projects\Model\Room');
        $room->setRoomName($params['roomNameInput']);
        $room->setProjectId($params['projectId']);
        $room->setRoomItems("[]");
        $room->save();

        return $room;
    }
}
