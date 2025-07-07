<?php
class EtudiantModel {
    private PDO $db;
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM etudiant");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM etudiant WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['age']
        ]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $data): int {
        $stmt = $this->db->prepare("UPDATE etudiant SET nom = ?, prenom = ?, email = ?, age = ? WHERE id = ?");
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['age'],
            $id
        ]);
        return $stmt->rowCount();
    }
    public function delete(int $id): int {
        $stmt = $this->db->prepare("DELETE FROM etudiant WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
