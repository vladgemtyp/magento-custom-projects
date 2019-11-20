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

class Project extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    protected function _construct()
    {
        $this->_init('Mediapark\Projects\Model\ResourceModel\Project');

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

    public function appendRooms($array){
        if(!is_array($array)){
            if(is_string($array)){
                $array = intval($array);
            }
            $array = array($array);
        }

        $rooms = $this->getProjectRooms();
        $rooms_decoded = [];
        if(!empty($rooms)){
            $rooms_decoded = json_decode($rooms);
        }
        $rooms_decoded = array_merge($rooms_decoded, $array);
        $rooms = json_encode($rooms_decoded);
        $this->setProjectRooms($rooms);
        $this->save();
    }

    public function removeRoom($id)
    {
        $projectItems = json_decode($this->getProjectRooms());
        $key = array_search($id, $projectItems);
        if (isset($key)){
            unset($projectItems[$key]);
            $projectItems = array_values($projectItems);
        }


        $this->setProjectRooms(json_encode($projectItems));

        $this->save();
    }
}