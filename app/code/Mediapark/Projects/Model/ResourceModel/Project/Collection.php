<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 */

namespace Mediapark\Projects\Model\ResourceModel\Project;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'project_id';
    protected $_eventPrefix = 'mediapark_projects_project_collection';
    protected $_eventObject = 'project_collection';

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Mediapark\Projects\Model\Project', 'Mediapark\Projects\Model\ResourceModel\Project');
    }
}