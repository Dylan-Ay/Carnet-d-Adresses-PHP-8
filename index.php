<?php
    require_once "controllers/ContactController.php";

    $contactController = new ContactController;

    if(isset($_GET['page'])){
        switch($_GET['page']){  
            case "contact":
                if(isset($_GET['action'])){
                    switch($_GET['action']){
                        case "add":
                            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $contactController->addContact();
                            }else{
                                $contactController->index();
                            }
                            break;
                        case "edit":
                            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $contactController->modifyContact();
                            }else{
                                $contactController->index();
                            }
                            break;
                        case "search":
                            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $contactController->searchContact();
                            }else{
                                $contactController->index();
                            }
                            break;

                        default:
                            header('Location: index.php?page=contact');
                            break;
                    }
                }else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["contact-id"])) {
                    $contactController->switchContactInformations();
                }else{
                    $contactController->index();
                }
                break;
            default:
                header('Location: index.php?page=contact');
                break;
        }
    }
    else{
        header('Location: index.php?page=contact');
    }
?>


