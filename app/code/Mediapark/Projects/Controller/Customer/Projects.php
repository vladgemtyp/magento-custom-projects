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

class Projects extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        RequestInterface $request
    ) {
        $this->_request = $request;
        $id = $this->_request->getParam('id');
        return parent::__construct($context);
    }

    public function execute()
    {
        $post = (array) $this->getRequest()->getPost();
        if (!empty($post)) {
            $projectId = $post['projectId'];
            if(!empty($post['roomName'])){
                // Retrieve your form data
                $roomName = $post['roomName'];
                //create new line in db
                $this->log(print_r($post));
                $room = $this->createRoom($roomName, $projectId);
                $roomId = $room->getRoomId();
                $project = $this->_objectManager->create('Mediapark\Projects\Model\Project')->load($projectId);
                //add created room to current project
                $project->appendRooms($roomId);
            }

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl('/projects/customer/projects?id=' . $projectId);

            return $resultRedirect;
        }
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if (!$customerSession->isLoggedIn()) {
            //if customer didn't log in -> redirect him to log in page
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');

            return $resultRedirect;
        }
        $var = $this->_request->getParam('id');
        if(!isset($var)){
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('projects/customer/index');
            return $resultRedirect;
        }


        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * @param $roomName
     * @param $projectId
     * @return mixed
     */
    public function createRoom($roomName, $projectId)
    {
        $room = $this->_objectManager->create('Mediapark\Projects\Model\Room');
        $room->setRoomName($roomName);
        $room->setProjectId($projectId);
        $room->setRoomItems("[]");
        $room->save();

        return $room;
    }

}
