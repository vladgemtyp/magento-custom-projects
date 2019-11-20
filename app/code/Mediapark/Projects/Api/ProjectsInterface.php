<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 */

namespace Mediapark\Projects\Api;

interface ProjectsInterface
{
    /**
     * Changes status for project
     *
     * @api
     * @param int $id project id.
     * @return string "ok" if success.
     */
    public function setProjectPendingStatus($id);

    /**
     * Changes status for project with value from query
     *
     * @api
     * @param string $id project id.
     * @param string $status.
     * @return string "{project_id: 'ID', status: 'STATUS'}"
     */
    public function setProjectStatus($id, $status);

    /**
     * Delete item from project by id
     *
     * @api
     * @param int $itemId
     */
    public function deleteItem($itemId);

    /**
     * Delete room from project by id
     *
     * @api
     * @param int $roomId.
     */
    public function deleteRoom($roomId);

    /**
     * Delete Project by id
     *
     * @api
     * @param int $projectId Users name.
     * @return string Greeting message with users name.
     */
    public function deleteProject($projectId);

    /**
     * Returns project Rooms by project id
     *
     * @api
     * @param int $id Users name.
     * @return string json with rooms.
     */
    public function projectRooms($id);


    /**
     * Returns projects by user id
     *
     * @api
     * @param int $userId Users name.
     * @return string json with projects.
     */
    public function userProjects($userId);


    /**
     * Add product by id to user's cart
     *
     * @api
     * @param int $id Users name.
     * @return string "success" or "User is not logged in"
     */
    public function addSingleProductToCart($id);
}