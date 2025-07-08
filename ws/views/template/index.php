<?php
$apiBase = "http://localhost/examen_web_s4/ws";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Etablissement Financier</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="public/template/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.html">
            <img src="public/images/mety.png" alt="Logo" style="height: 80px; width: 80px; margin-left: 40px; margin-top: 5px;">
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i
                class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user-circle"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#">Paramètres</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="#">Déconnexion</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu Principal</div>
                        <a class="nav-link" href="index.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Tableau de bord
                        </a>
                        <a class="nav-link" href="<?php echo $apiBase ?>/formFond">
                            <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                            Fonds
                        </a>
                        <a class="nav-link" href="<?php echo $apiBase ?>/typepret">
                            <div class="sb-nav-link-icon"><i class="fas fa-percent"></i></div>
                            Types de prêt
                        </a>
                        <a class="nav-link" href="" data-bs-toggle="collapse" data-bs-target="#collapsePret"
                            aria-expanded="false">
                            <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-usd"></i></div>
                            Gestion de prêts
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePret" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="<?php echo $apiBase ?>/pret"><i
                                        class="fas fa-file-signature me-2"></i>Formulaire prêt</a>
                                <a class="nav-link" href="<?php echo $apiBase ?>/pendingPretPage"><i class="fas fa-list me-2"></i>Liste des
                                    prêts à valider</a>
                                <a class="nav-link" href="<?php echo $apiBase ?>/interets"><i class="fas fa-chart-line me-2"></i>Voir les
                                    intérêts</a>
                                <a class="nav-link" href="<?php echo $apiBase ?>/clients/avec-prets"><i class="fas fa-file-pdf me-2"></i>Voir PDF
                                    prêt</a>
                                <a class="nav-link" href="<?php echo $apiBase ?>/remboursements/attente/liste"><i
                                        class=" fas fa-check-circle me-2"></i>Valider remboursement</a>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Connecté en tant que :</div>
                    Administrateur
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php if (isset($page)) {
                        include(__DIR__ . '/../' . $page . '.php');
                    } ?>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Votre Application 2025</div>
                        <div>
                            <a href="#">Politique de confidentialité</a>
                            &middot;
                            <a href="#">Conditions d'utilisation</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="public/template/js/scripts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const current = window.location.href.split('#')[0].replace(/\/$/, '');
            document.querySelectorAll('#sidenavAccordion .nav-link[href]').forEach(link => {
                const linkHref = link.href.split('#')[0].replace(/\/$/, '');
                if (linkHref && (current === linkHref || current.startsWith(linkHref))) {
                    link.classList.add('active');
                    const collapse = link.closest('.collapse');
                    if (collapse) {
                        collapse.classList.add('show');
                        const parent = collapse.previousElementSibling;
                        if (parent && parent.classList.contains('nav-link')) {
                            parent.classList.add('active');
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>