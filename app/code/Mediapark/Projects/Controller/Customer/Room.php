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

use Magento\Framework\App\RequestInterface;

class Room extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        RequestInterface $request
    ) {
        return parent::__construct($context);
    }

    public function execute()
    {
        $post = (array) $this->getRequest()->getPost();
        if (!empty($post)) {
            // Retrieve your form data
            $roomName = $post['roomName'];
            // get project id
            $projectId = $this->getProjectIdFromUrl();
            // create room in db
            $room = $this->createRoom($roomName);
            // append room in project
            $roomId = $room->getRoomId();
            $project = $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($projectId);
            $project->appendRooms($roomId);
            // reload page
            // Doing-something with...
            // Display the succes form validation message
            $this->_messageManager->addSuccessMessage('Booking done !');
            // Redirect to your form page (or anywhere you want...)
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('/companymodule/index/booking');

            return $resultRedirect;
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * @param $roomName
     * @return mixed
     */
    public function createRoom($roomName)
    {
        $room = $this->_objectManager->create('Mediapark\Projects\Model\Room');
        $room->setRoomName($roomName);
        $room->setRoomItems("[]");
        $room->save();

        return $room;
    }
}
