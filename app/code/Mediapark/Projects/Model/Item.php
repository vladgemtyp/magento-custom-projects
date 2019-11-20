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

class Item extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface

{
    const CACHE_TAG = 'mediapark_project_item';

    protected $_cacheTag = 'mediapark_project_item';

    protected $_eventPrefix = 'mediapark_project_item';

    protected function _construct()
    {
        $this->_init('Mediapark\Projects\Model\ResourceModel\Item');
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
}