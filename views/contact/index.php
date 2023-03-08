<!DOCTYPE html>
<html lang="fr">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.2.3/dist/zephyr/bootstrap.min.css" rel="stylesheet">   
    <link rel="stylesheet" href="./public/css/style.css">
    <script src="https://kit.fontawesome.com/aadee783c9.js" crossorigin="anonymous"></script>
    <title>Test Saabre</title>
  </head>
  <body>
        <header class="bg-dark">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container pt-1">
                    <div class="d-flex justify-content-between w-100">
                    <a class="navbar-brand" href="index.php?page=contact">Test Saabre</a>
                    <a class="navbar-brand" href="https://dylanayache.000webhostapp.com/" target="_blank">Dylan Ayache</a>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container py-5 px-1">
                <h1 class="text-center">Carnet d'adresses</h1>
                
                <!-- Message de succès -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success my-3 text-center w-lg-75 m-auto" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                <?php unset($_SESSION['success_message']); ?>

                <!-- Message d'erreur -->
                <?php elseif (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger my-3 text-center w-lg-75 m-auto" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <!-- Carnet d'adresses -->
                <section id="address-book" class="mt-4 p-md-4 rounded-1">
                    <!-- Barre de recherche -->
                    <div class="input-group justify-content-between">
                        <form id="search-form" class="form-outline d-flex w-100" action="index.php?page=contact&action=search" method="POST">
                            <input id="search-input" name="search" type="search" class="form-control py-2" placeholder="Rechercher un contact (ex: téléphone, nom et prénom, email)" value="<?= isset($_POST['search']) ? $_POST['search'] : ''; ?>">
                            <button type="submit" id="search-button" class="btn btn-primary">
                                <i class='fas fa-search'></i>
                            </button>
                        </form>
                    </div>
                    <div class="row w-100 m-auto">
                        <!-- Zone d'affichage des contacts -->
                        <div class="col-md-6 left-block p-3">
                            <h3>Tous les contacts</h3>
                            <small>Veuillez sélectionner un contact pour le modifier ou afficher ses informations.</small>
                            <?php if (isset($contacts)):?>
                            <ul id="contact-list" class="list-unstyled ps-3 pt-2 overflow-auto">
                                <?php foreach ($contacts as $contact):?>
                                    <li>
                                        <input type="radio" name="contact-choice" id="contact-choice-<?=$contact['id']?>" value="<?= $contact['id']?>" <?php if(isset($_POST['id']) && $_POST['id'] == $contact['id']):?> checked <?php endif;?>>

                                        <label for="contact-choice-<?= $contact['id']?>">
                                            <?= $contact['lastname'].' '. $contact['firstname'] ?>
                                        </label>
                                    </li>
                                <?php endforeach?>
                            </ul>
                            <?php else: ?>
                                <p class="pt-3 text-center">Aucun résultat ne correspond à la recherche : <br>
                                    <span class="fw-bolder">"<?= isset($_POST['search']) ? $_POST['search'] : ''; ?>"</span>
                                </p>
                            <?php endif; ?>
                            <!-- Boutons d'actions de la zone contacts -->
                            <button id="btn-action-contact" class="btn btn-primary d-block rounded-5 mt-5 w-75 m-auto">Nouveau contact</button>
                            <a class="btn btn-dark d-block rounded-5 mt-3 mb-2 mb-lg-0 w-75 m-auto" href="index.php?page=contact">Afficher tous les contacts</a>
                        </div>
                        <!-- Zone d'édition d'un contact -->
                        <div class="col-md-6 right-block p-3">
                            <h3 id="title-contact-block">Modifier un contact</h3>
                            <form id="contact-form" class="edit" action="index.php?page=contact&action=edit" method="POST">
                                <label class="pt-3" for="lastname">Nom *</label>
                                <input class="form-control modify-input" type="text" name="lastname" id="lastname" value="<?= isset($_POST['lastname']) ? $_POST['lastname'] : ''; ?>" required >

                                <label class="pt-3" for="firstname">Prénom *</label>
                                <input class="form-control modify-input" type="text" name="firstname" id="firstname" value="<?= isset($_POST['firstname']) ? $_POST['firstname'] : ''; ?>" required >

                                <label class="pt-3" for="email">Adresse mail *</label>
                                <input class="form-control modify-input" type="email" name="email" id="email" value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>" required >

                                <label class="pt-3" for="tel">Téléphone *</label>
                                <input class="form-control modify-input" type="tel" name="tel" id="tel" maxlength="10" value="<?= isset($_POST['tel']) ? $_POST['tel'] : ''; ?>" required >
                                
                                <label class="pt-3" for="city">Ville *</label>
                                <select class="form-select modify-input" name="city" id="city" required >
                                    <?php isset($_POST['city']) ? $_POST['city'] : ''; ?>
                                    <option value="" selected>Choisissez une ville</option>
                                    <option value="Lyon" <?= (isset($_POST['city']) && $_POST['city'] === 'Lyon') ? 'selected' : '' ?>>Lyon</option>
                                    <option value="Marseille" <?= (isset($_POST['city']) && $_POST['city'] === 'Marseille') ? 'selected' : '' ?>>Marseille</option>
                                    <option value="Paris" <?= (isset($_POST['city']) && $_POST['city'] === 'Paris') ? 'selected' : '' ?>>Paris</option>
                                </select>
                                <input type="hidden" name="id" id="contact-id" value="<?= isset($_POST['id']) ? $_POST['id'] : ''; ?>">

                                <!-- Boutons d'actions de la zone d'édition -->
                                <div class="row mt-4">
                                    <div class="col-6">
                                        <input class="btn btn-success rounded-5 w-100" type="submit" value="Valider">
                                    </div>

                                    <div class="col-6">
                                        <a id="cancel-btn" class="btn btn-secondary rounded-5 w-100">Annuler</a>
                                    </div>
                                </div>  
                            </form>            
                        </div>
                    </div>
                </section>
            </div>
        </main>
        <script src="/public/js/app.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    </body>
</html>
