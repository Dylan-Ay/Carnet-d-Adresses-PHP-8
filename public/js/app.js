// Variables
const titleContactBlock = document.getElementById('title-contact-block');
const editContactBtn = document.getElementById('btn-action-contact');
const cancelBtn = document.getElementById('cancel-btn');
const formInputs = document.querySelectorAll('form .modify-input');
const contactForm = document.getElementById('contact-form');
const alertMessage = document.querySelector('.alert');
const contactListInputs = document.querySelectorAll('#contact-list li input[type="radio"]');
const searchInput = document.getElementById('search-input');
const params = new URLSearchParams(window.location.search);

// Définit l'action et le titre du block d'édition de contact
function setFormActionAndTitle(title, action) {
    titleContactBlock.innerHTML = title;
    contactForm.action = action;
}

// Vérifie quelle action a été envoyé en GET
// Dans le cas où le form génère un message d'erreur cela permet de renvoyer le form de création de contact
function checkAction(){
    const action = params.get('action');
    
    if (action === "add") {
        addContact(false);
    }
}
checkAction();

// Permet de décocher un input radio dans le cas où il y en a un de coché
function uncheckedRadioInputs(){
    contactListInputs.forEach(input => {
        if (input.checked) {
            input.checked = false;
        }
    });
}

// Reset les champs inputs
function resetInputs(){
    formInputs.forEach(input => {
        input.value = "";
    });
}

// Reset le form à zéro
function cancelAction (){
    //Vide le champ recherche
    searchInput.value = "";

    // Remove le message d'alert s'il existe
    alertMessage?.remove();

    // Reset les champs inputs
    resetInputs();
}

// Défini les diffénts paramètres pour la création d'un contact
function addContact(useCancelAction = true){

    // Change l'html du btn d'action
    editContactBtn.innerHTML = "Modifier un contact";

    // Update l'action du form
    setFormActionAndTitle("Création d'un contact", "index.php?page=contact&action=add");

    // Supprime la class edit au form
    contactForm.classList.remove("edit");

    // Décoche tous les inputs radios si il y en a un de coché
    uncheckedRadioInputs(contactListInputs);

    // Reset le form à zéro
    if (useCancelAction) {
        cancelAction();
    }
}

// Défini les diffénts paramètres pour la modification d'un contact
function modifyContact() {

    // Change l'html du btn d'action
    editContactBtn.innerHTML = "Nouveau contact";

    // Update l'action du form
    setFormActionAndTitle("Modifier un contact", "index.php?page=contact&action=edit");

    // Ajoute la class edit au form
    contactForm.classList.add("edit");
    
    // Reset le form à zéro
    cancelAction();
}

// Appelle une fonction selon la class contenu par le form
editContactBtn.addEventListener('click', function(){
    if (contactForm.classList.contains('edit')) {
        addContact();
    }else{
        modifyContact();
    }
})

// Appelle la fonction cancelAction et uncheckedRadioInputs lors du clique sur le btn "annuler"
cancelBtn.addEventListener('click', function(){
    cancelAction();
    uncheckedRadioInputs();
})

// Requête Ajax pour changer dynamiquement les informations du form
// Selon le contact sélectionné
const contactId = document.getElementById('contact-id');
const contactFirstname = document.getElementById('firstname');
const contactLastname = document.getElementById('lastname');
const contactEmail = document.getElementById('email');
const contactTel = document.getElementById('tel');
const contactCity = document.getElementById('city');

contactListInputs.forEach(input => {
    input.addEventListener('change', function(){

        // Défini les diffénts paramètres pour la modification d'un contact
        modifyContact();

        $(document).ready(function() {
            // Récupère l'id du contact sélectionné
            const id = input.value;

            // Envoie la valeur au controller
            $.ajax({
                type: "POST",
                url: "index.php?page=contact",
                data: {
                    'contact-id': id
                },
                dataType: 'json',
                cache: false,
                success: function(response) {
                    // Si le controller renvoie error 
                    if (response.error === true) {
                        console.error("Request failed: " + response.error);
                        alert("Le contact n'existe pas.");
                     
                    }else{
                        // Sinon update les champs de chaque input avec la valeur adéquate
                        contactId.value = response.contact.id;
                        contactFirstname.value = response.contact.firstname;
                        contactLastname.value = response.contact.lastname;
                        contactEmail.value = response.contact.email;
                        contactTel.value = response.contact.tel;
                        contactCity.value = response.contact.city;
                    }
                },
                error: function(response) {
                    console.error("Request failed: " + response.error);
                    alert("Une erreur est survenue");
                },
            });
        });
    })
});