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
use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Framework\App\Action\Action
{
    protected $formKey;

    protected $cart;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        RequestInterface $request,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->_request = $request;
        $this->formKey = $formKey;
        $this->cart = $cart;

        return parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->create("Magento\Customer\Model\Session");
        if ($customerSession->isLoggedIn()) {

            $post = (array) $this->getRequest()->getPost();
            if (array_key_exists('form_key', $post)) {
                unset($post['form_key']);
            }
            foreach ($post as $itemId) {
                //get item object to get poduct id
                // we can increase productivity by saving product id in layout, but it takes more time
                $itemObject = $this->getObject($itemId, "Item");
                //get procut entity
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                    ->load($itemObject->getData()['product_id']);
                //take back user params for this product (color, etc)
                $params = json_decode($itemObject->getData()['params'], true);
                //add prod to cart
                $this->cart->addProduct($product, $params);
            }
            $this->cart->save();
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
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