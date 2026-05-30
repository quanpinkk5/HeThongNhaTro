<?php
require_once __DIR__ . '/../../models/customer/FavoriteModel.php';

class FavoriteController {
    private $favModel;

    public function __construct($db) {
        $this->favModel = new FavoriteModel($db);
    }

    public function getFavorites($userId) {
        return $this->favModel->getFavoritesByUser($userId);
    }

    public function handleToggle($userId, $roomId) {
        return $this->favModel->toggleFavorite($userId, $roomId);
    }
}