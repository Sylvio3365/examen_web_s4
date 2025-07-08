<?php
$apiBase = "http://localhost/examen_web_s4/ws";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Etablissement Financier</title>

    <!-- STYLES -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="public/template/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <!-- NAVBAR -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.html">
            <img src="public/images/mety.png" alt="Logo" style="height: 80px; width: 80px; margin-left: 40px; margin-top: 5px;">
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#">Paramètres</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="<?= $apiBase ?>/logout">Déconnexion</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- LAYOUT -->
    <div id="layoutSidenav">
        <!-- SIDEBAR -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <div class="sb-sidenav-menu-heading">Menu principal</div>

                        <a class="nav-link" href="index.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Tableau de bord
                        </a>

                        <a class="nav-link" href="<?= $apiBase ?>/formFond">
                            <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                            Fonds disponibles
                        </a>

                        <div class="sb-sidenav-menu-heading">Prêts</div>

                        <a class="nav-link" href="<?= $apiBase ?>/typepret">
                            <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
                            Types de prêt
                        </a>

                        <a class="nav-link" href="<?= $apiBase ?>/pret">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus-circle"></i></div>
                            Nouveau prêt
                        </a>

                        <a class="nav-link" href="<?= $apiBase ?>/pendingPretPage">
                            <div class="sb-nav-link-icon"><i class="fas fa-hourglass-half"></i></div>
                            Prêts à valider
                        </a>

                        <a class="nav-link" href="<?= $apiBase ?>/clients_pret">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-pdf"></i></div>
                            Générer PDF prêts
                        </a>

                        <div class="sb-sidenav-menu-heading">Suivi & Analyse</div>

                        <a class="nav-link" href="<?= $apiBase ?>/remboursements_attente">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-check-alt"></i></div>
                            Valider remboursement
                        </a>

                        <a class="nav-link" href="<?= $apiBase ?>/interets">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                            Intérêts par mois
                        </a>

                        <a class="nav-link" href="<?= $apiBase ?>/montanttotal">
                            <div class="sb-nav-link-icon"><i class="fas fa-balance-scale"></i></div>
                            Solde disponible
                        </a>

                        <a class="nav-link" href="<?= $apiBase ?>/comparaison">
                            <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                            Comparer 2 prêts
                        </a>

                    </div>
                </div>
            </nav>
        </div>

        <!-- CONTENU PRINCIPAL -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php if (isset($page)) include(__DIR__ . '/../' . $page . '.php'); ?>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; Etablissement Financier 2025</div>
                        <div>
                            <a href="#">Politique de confidentialité</a> &middot;
                            <a href="#">Conditions d'utilisation</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="public/template/js/scripts.js"></script>

    <script>
        // Activer le lien actif dans la sidebar
        document.addEventListener('DOMContentLoaded', () => {
            const current = window.location.href.replace(/\/$/, '');
            document.querySelectorAll('#sidenavAccordion .nav-link').forEach(link => {
                const linkHref = link.href ? link.href.replace(/\/$/, '') : null;
                if (linkHref && current === linkHref) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>