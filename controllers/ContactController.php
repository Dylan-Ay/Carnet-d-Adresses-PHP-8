<?php

require_once "config/database.php";
require_once 'models/ContactModel.php';
require_once "entities/Contact.php";

class ContactController
{
    private $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactModel;
    }

    // Récupère la liste de contacts
    private function getContactsList(): Object
    {
        return $contacts = $this->contactModel->findAll();
    }

    // Réinitialise les variables du formulaire pour ne pas les afficher en value
    private function clearPostInputs(array $array){
        foreach ($array as $input) {
            $_POST[$input] = null;
        }
    }
    
    // Affiche la page du form par défaut
    public function index(): void
    {
        $contacts = $this->getContactsList();

        require "views/contact/index.php";
    }

    // Ajoute un nouveau contact
    public function addContact(): void
    {   
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Filtre les données reçues
            $firstname = trim(filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $lastname = trim(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
            $tel = trim(filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            // Vérifie les champs requis et les formats valides
            if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($tel) && !empty($city) ) {
                if (preg_match('/^[0-9]{10}$/', $tel)) {
                    if (preg_match('/^[A-Za-z\-]+$/', $firstname) && preg_match('/^[A-Za-z\-]+$/', $lastname)) {
                        if (in_array($city, ["Lyon", "Marseille", "Paris"], true)) {

                            $contact = new Contact(null, $firstname, strtoupper($lastname), $email, $tel, $city);
                            
                            // Vérifie que l'adresse mail n'existe pas déjà
                            if (!$this->contactModel->checkEmailExists($email)) {
                                try {
                                    $this->contactModel->add($contact);

                                    // Message de succès indiquant que le contact a bien été créé
                                    $_SESSION['success_message'] = "Le contact $firstname $lastname a bien été ajouté au carnet d'adresses.";
        
                                    // Réinitialise les variables du formulaire pour ne pas les afficher en value
                                    $this->clearPostInputs(['firstname', 'lastname', 'email', 'tel', 'city']);
                                    
                                } catch (PDOException) {
                                    // Message d'erreur si le contact existe déjà (si l'adresse mail existe).
                                    $_SESSION['error_message'] = "L'adresse mail de ce contact existe déjà dans le carnet d'adresses.";
                                }
                            }
                        }
                        else{
                            // Message d'erreur si la ville ne correspond pas aux villes de la liste.
                            $_SESSION['error_message'] = "La ville doit être Lyon, Marseille ou Paris.";
                        }
                    }else{
                        // Message d'erreur si le nom ou le prénom ne contiennent pas exclusivement des lettres.
                        $_SESSION['error_message'] = "Le nom et le prénom doivent être composés exclusivement de lettres.";
                    }      
                }else{
                    // Message d'erreur si le numéro de téléphone n'a pas 10 caractères uniquement composé de chiffres.
                    $_SESSION['error_message'] = "Le numéro de téléphone doit contenir 10 chiffres.";
                }
            }else{
                // Message d'erreur si les champs sont vides ou que le format n'est pas respecté.
                $_SESSION['error_message'] = "Tous les champs doivent être remplis avec le format adéquat.";
            }      
        }

        $this->index();
    }

    // Change les informations dans le form, suivant le contact sélectionné grâce à une requête AJAX
    public function switchContactInformations(): void
    {   
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Filtre l'input reçu (id du contact)
            $id = filter_input(INPUT_POST, 'contact-id', FILTER_VALIDATE_INT);
            
            // Récupère le contact dont l'id est celui reçu de la requête Ajax
            $contact = $this->contactModel->findOneById($id);
                    
            if(!$contact){
                header('Content-Type: application/json');
                echo json_encode(['error' => true]);
                return;
            }else{
                header('Content-Type: application/json');
                echo json_encode(['error' => false, 'contact' => $contact]);
                return;
            }
        }
    }

    // Modifie un contact
    public function modifyContact(): void
    {   
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            
            // Filtre les données reçues
            $firstname = trim(filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $lastname = trim(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
            $tel = trim(filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            
            // Vérifie les champs requis et les formats valides
            if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($tel) && !empty($city) && !empty($id) ) {
                if (preg_match('/^[0-9]{10}$/', $tel)) {
                    if (preg_match('/^[A-Za-z\-]+$/', $firstname) && preg_match('/^[A-Za-z\-]+$/', $lastname)) {
                        if (in_array($city, ["Lyon", "Marseille", "Paris"], true)) {

                            $contact = new Contact($id, $firstname, strtoupper($lastname), $email, $tel, $city);

                            // Vérifie que l'adresse mail n'existe pas déjà
                            if (!$this->contactModel->checkEmailExists($email)) {
                                try {
                                    $this->contactModel->update($contact);

                                    // Message de succès indiquant que le contact a bien été mis à jour
                                    $_SESSION['success_message'] = "Le contact $firstname $lastname a bien été mis à jour dans le carnet d'adresses.";
    
                                    // Réinitialise les variables du formulaire pour ne pas les afficher en value
                                    $this->clearPostInputs(['id', 'firstname', 'lastname', 'email', 'tel', 'city']);
                                    
                                } catch (PDOException) {
                                    // Message d'erreur si le contact existe déjà (si l'adresse mail existe).
                                    $_SESSION['error_message'] = "Cette adresse mail appartient déjà à un contact, veuillez modifier votre saisie.";
                                }
                            }
                        }else{
                            // Message d'erreur si la ville ne correspond pas aux villes de la liste.
                            $_SESSION['error_message'] = "La ville doit être Lyon, Marseille ou Paris.";
                        }
                    }else{
                        // Message d'erreur si le nom ou le prénom ne contiennent pas exclusivement des lettres.
                        $_SESSION['error_message'] = "Le nom et le prénom doivent être composés exclusivement de lettres.";
                    }      
                }else{
                    // Message d'erreur si le numéro de téléphone n'a pas 10 caractères uniquement composé de chiffres.
                    $_SESSION['error_message'] = "Le numéro de téléphone doit contenir 10 chiffres.";
                }
            }else{
                // Message d'erreur si les champs sont vides ou que le format n'est pas respecté.
                $_SESSION['error_message'] = "Tous les champs doivent être remplis avec le format adéquat.";
            }
        }

        $this->index();
    }

    // Recherche un contact par téléphone, email ou par nom et prénom
    public function searchContact(): void
    {
        // Récupère la valeur de $_POST['search'] si définie
        $searchInput = trim($_POST['search'] ?? '');
       
        if ($_SERVER["REQUEST_METHOD"] === "POST" && $searchInput) {

            // Si la recherche est un numéro composé de 10 chiffres
            if (preg_match('/^[0-9]{10}$/', $searchInput)){

                // Filtre l'input
                $searchInput = trim(filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                
                // Sauvegarde du résultat de la recherche dans $contacts
                $contacts = $this->contactModel->searchByTel($searchInput);
            }

            // Sinon si la recherche est un string et qu'elle ne comporte pas de chiffres
            else if (preg_match('/^[a-zA-Z ]+$/', $searchInput) && !filter_var($searchInput, FILTER_VALIDATE_EMAIL)) {

                // Filtre l'input
                $searchInput = trim(filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

                // Séparation des 2 valeurs recherchées dans un tableau
                $fullName = explode(' ', $searchInput);
                
                // Si le tableau contient 2 valeurs, alors je récupère chacune des valeurs
                // Chaque valeur est envoyé dans searchByFullName
                if (count($fullName) == 2) {
                    $lastnameSearch = $fullName[0];
                    $firstnameSearch = $fullName[1];
    
                    $contacts = $this->contactModel->searchByFullName($firstnameSearch, $lastnameSearch);
                }else{
                    // Message d'erreur s'il n'y a pas 2 mots clés insérés
                    $_SESSION['error_message'] = "Veuillez insérer un nom et un prénom.";
                    $contacts = $this->getContactsList();
                }

            // Sinon si la recherche est une adresse mail
            }else if (filter_var($searchInput, FILTER_VALIDATE_EMAIL)){

                // Filtre l'input reçu
                $searchInput = trim(filter_input(INPUT_POST, 'search', FILTER_SANITIZE_EMAIL));

                // Sauvegarde du résultat de la recherche dans $contacts
                $contacts = $this->contactModel->searchByEmail($searchInput);

            }else{
                // Message d'erreur si le numéro de téléphone n'a pas 10 caractères uniquement composé de chiffres.
                $_SESSION['error_message'] = "Veuillez insérer un numéro de téléphone contenant 10 chiffres.";
                $contacts = $this->getContactsList();
            }
            
        }else{
            // Message d'erreur si le champ de recherche est vide.
            $_SESSION['error_message'] = "Veuillez insérer une donnée valide dans la recherche.";
            $contacts = $this->getContactsList();
        }
        
        require "views/contact/index.php";
    }
}