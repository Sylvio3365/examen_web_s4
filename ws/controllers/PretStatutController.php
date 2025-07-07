<?php
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/PretStatut.php';

class PretStatutController{
    public static function insererPretstatut(){
        $data = Flight::request()->data;
        $id = PretStatut::insertPretStatut($data);
        Flight::json(['message'=> 'pretStatut ajoute','id'=> $id]);
    }

}