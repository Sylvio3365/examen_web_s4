<?php
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class TypePretController
{

    public static function goIndex()
    {
        Flight::render('TypePret/index');
    }
    public static function getAll()
    {
        $etudiants = TypePret::getAll();
        Flight::json($etudiants);
    }

    public static function getById($id)
    {
        $etudiant = TypePret::getById($id);
        Flight::json($etudiant);
    }

    public static function create()
    {
        $data = Flight::request()->data;
        $id = TypePret::create($data);
        Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id]);
    }

    public static function update($id)
    {
        $data = Flight::request()->data;
        TypePret::update($id, $data);
        Flight::json(['message' => 'Type de prêt modifié']);
    }

    public static function delete($id)
    {
        TypePret::delete($id);
        Flight::json(['message' => 'Type de prêt supprimé']);
    }
}
