<?php

require_once "config/database.php";

class ContactModel{

    private Object $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Récupère la liste des contacts
    public function findAll(): Object
    {
        $query = "SELECT * FROM contact ORDER BY lastname ASC";
        $result = $this->db->executeRequest($query);

        return $result;
    }

    // Récupère un contact grâce à son identifiant
    public function findOneById(int $id): array
    {
        $query = "SELECT * FROM contact WHERE id = :id";
        $prepareAndExecute = $this->db->executeRequest($query, ['id' => $id]);
        $result = $prepareAndExecute->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    // Créer un contact
    public function add(Contact $contact): void
    {
        $query = 
        "INSERT INTO contact(firstname, lastname, email, tel, city) VALUES (:firstname, :lastname, :email, :tel, :city)";

        $prepareAndExecute = $this->db->executeRequest($query, [
            'firstname' => $contact->getFirstname(),
            'lastname' => $contact->getLastname(),
            'email' => $contact->getEmail(),
            'tel' => $contact->getTel(),
            'city' => $contact->getCity()
        ]);
    }

    // Modifie un contact
    public function update(Contact $contact): void
    {
        $query =
        "UPDATE contact SET firstname = :firstname, lastname = :lastname, email = :email, tel = :tel, city = :city
        WHERE id = :id
        ";
        $prepareAndExecute = $this->db->executeRequest($query, [
            'id' => $contact->getId(),
            'firstname' => $contact->getFirstname(),
            'lastname' => $contact->getLastname(),
            'email' => $contact->getEmail(),
            'tel' => $contact->getTel(),
            'city' => $contact->getCity()
        ]);
    }

    // Vérifie que le contact n'existe pas déjà dans la db par rapport à son email
    public function checkEmailExists(string $email): void
    {
        $query = 
        "SELECT COUNT(*) FROM contact WHERE email = :email;";
        
        $prepareAndExecute = $this->db->executeRequest($query, [
            'email' => $email
        ]);
    }

    // Effectue une recherche par nom et prénom
    public function searchByFullName(string $firstname, string $lastname): ?Object
    {
        $query = 
        "SELECT * FROM contact WHERE (firstname LIKE :firstname AND lastname LIKE :lastname) OR (firstname LIKE :lastname AND lastname LIKE :firstname) ORDER BY lastname ASC";
        
        $result = $this->db->executeRequest($query, [
            'firstname' => "%$firstname%",
            'lastname' => "%$lastname%"
        ]);

        // Vérifie si un résultat existe
        if ($result->rowCount() == 0) {
            return null;
        }

        return $result;
    }

    // Effectue une recherche par téléphone
    public function searchByTel(string $tel): ?Object
    {
        $query = 
        "SELECT * FROM contact WHERE tel LIKE :tel ORDER BY lastname ASC";

        $result = $this->db->executeRequest($query, [
            'tel' => "%$tel%",
        ]);

        // Vérifie si un résultat existe
        if ($result->rowCount() == 0) {
            return null;
        }

        return $result;
    }

    // Effectue une recherche par mail
    public function searchByEmail(string $email): ?Object
    {
        $query = 
        "SELECT * FROM contact WHERE email LIKE :email ORDER BY lastname ASC";

        $result = $this->db->executeRequest($query, [
            'email' => "%$email%",
        ]);

        // Vérifie si un résultat existe
        if ($result->rowCount() == 0) {
            return null;
        }

        return $result;
    }
}