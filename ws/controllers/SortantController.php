<?php
require_once __DIR__ . '/../models/Sortant.php';
require_once __DIR__ . '/../helpers/Utils.php';

class SortantController{
    public static function insererSortant(){
        $data = Flight::request()->data;
        $id = Sortant::insertSortant($data);
        Flight::json(['message'=> 'sortant ajoute','id'=> $id]);
        
    }
}