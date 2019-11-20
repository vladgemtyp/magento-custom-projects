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

use Magento\Framework\Model\AbstractModel;

class Room extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    protected function _construct()
    {
        $this->_init('Mediapark\Projects\Model\ResourceModel\Room');
    }

    public function getIdentities()
    {
        //return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    public function appendItems($array){
        if(!is_array($array)){
            if(is_string($array)){
                $array = intval($array);
            }
            $array = array($array);
        }
        $items = $this->getRoomItems();
        $items_decoded = [];
        if(!empty($items)){
            $items_decoded = json_decode($items);
        }
        $items_decoded = array_merge($items_decoded, $array);
        $items = json_encode($items_decoded);
        $this->setRoomItems($items);
        $this->save();
    }
    public function removeItem($id)
    {
        $roomItems = json_decode($this->getRoomItems());
        $key = array_search($id, $roomItems);
        $res = [];
        if (isset($key)){
            unset($roomItems[$key]);
            $roomItems = array_values($roomItems);
        }
        $res = json_encode($roomItems);
        $this->setRoomItems(json_encode($roomItems));
        $this->save();
    }
}