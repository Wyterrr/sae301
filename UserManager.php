<?php 
class UserManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Créer un utilisateur
    public function createUser($user) {
        $query = $this->db->prepare(
            "INSERT INTO users (name, email, password, adresse, telephone, role, creation)
             VALUES (:name, :email, :password, :adresse, :telephone, :role, :creation)"
        );

        $query->execute([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => password_hash($user->getPassword(), PASSWORD_BCRYPT),
            'adresse' => $user->getAdresse(),
            'telephone' => $user->getTelephone(),
            'role' => $user->getRole(),
            'creation' => date('Y-m-d H:i:s'),
        ]);
    }

    // Récupérer un utilisateur par ID
    public function getUserById($id) {
        $query = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch();
        return $data ? $this->mapToUser($data) : null;
    }

    // Récupérer un utilisateur par email
    public function getUserByEmail($email) {
        $query = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        $data = $query->fetch();
        return $data ? $this->mapToUser($data) : null;
    }

    // Vérifier le login (email + mot de passe)
    public function verifyLogin($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user->getPassword())) {
            return $user;
        }
        return null;
    }

    // Mettre à jour les informations utilisateur
    public function updateUser($user) {
        $query = $this->db->prepare(
            "UPDATE users SET name = :name, email = :email, adresse = :adresse, telephone = :telephone, role = :role
             WHERE id = :id"
        );

        $query->execute([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'adresse' => $user->getAdresse(),
            'telephone' => $user->getTelephone(),
            'role' => $user->getRole(),
            'id' => $user->getId(),
        ]);
    }

    // Mapper les données SQL vers un objet User
    private function mapToUser($data) {
        $user = new User();
        $user->setId($data['id']);
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setAdresse($data['adresse']);
        $user->setTelephone($data['telephone']);
        $user->setRole($data['role']);
        $user->setCreation($data['creation']);
        return $user;
    }
}

?>