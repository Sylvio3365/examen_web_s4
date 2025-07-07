<?php
require_once __DIR__ . '/../models/EtudiantModel.php';

class EtudiantService {
    private EtudiantModel $model;

    public function __construct() {
        $this->model = new EtudiantModel(getDB());
    }

    public function getAll() {
        Flight::json($this->model->all());
    }

    public function get($id) {
        Flight::json($this->model->get($id));
    }

    public function create() {
        $data = Flight::request()->data->getData();
        $id = $this->model->create($data);
        Flight::json(['message' => 'Étudiant ajouté', 'id' => $id]);
    }

    public function update($id) {
        $data = Flight::request()->data->getData();
        $this->model->update($id, $data);
        Flight::json(['message' => 'Étudiant modifié']);
    }

    public function delete($id) {
        $this->model->delete($id);
        Flight::json(['message' => 'Étudiant supprimé']);
    }
}
