<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';

class PretController
{
    public static function goIndex()
    {
        Flight::render('pret/index');
    }
}
