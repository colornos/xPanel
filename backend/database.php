<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="xPanel dashboard.">
    <meta name="keywords" content="Control Panel">
    <meta name="author" content="Colornos">
    <title>xPanel</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/material-icons/material-icons.css">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/material-vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/bootstrap-treeview.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/material.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/material-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/material-colors.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/material-vertical-compact-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/material-palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/mobiriseicons/24px/mobirise/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/extensions/ex-component-tree-views.css">
    <!-- END: Page CSS-->
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-compact-menu material-vertical-layout material-layout 1-column fixed-navbar" data-open="click" data-menu="vertical-compact-menu" data-col="1-column">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-light navbar-shadow navbar-brand-center">
        <div class="navbar-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                    <li class="nav-item"><a class="navbar-brand" href="index.php"><img class="brand-logo" alt="x" src="app-assets/images/logo/logo.png">
                            <h3 class="brand-text">xPanel</h3>
                        </a></li>
                    <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END: Header-->

<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <div class="container-fluid mt-2">
                <div class="row">
                    <!-- Searchable Tree Column -->
                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Searchable Tree</h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="seachbox mb-2">
                                            <input type="text" class="form-control" placeholder="Search" id="input-search" name="input-search">
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-sm-12">
                                            <div class="form-group d-flex flex-wrap align-items-center mb-0">
                                                <div class="checkbox mr-1 mb-50">
                                                    <input type="checkbox" class="checkbox__input" id="chk-ignore-case" value="false">
                                                    <label for="chk-ignore-case">Ignore Case</label>
                                                </div>
                                                <div class="checkbox mr-1 mb-50">
                                                    <input type="checkbox" class="checkbox__input" id="chk-exact-match" value="false">
                                                    <label for="chk-exact-match">Exact Match</label>
                                                </div>
                                                <div class="checkbox mr-1 mb-50">
                                                    <input type="checkbox" class="checkbox__input" id="chk-reveal-results" value="false">
                                                    <label for="chk-reveal-results">Reveal Results</label>
                                                </div>
                                                <div class="searchable-action">
                                                    <button type="button" class="btn btn-primary btn-sm mr-1 mb-50" id="btn-search">Search</button>
                                                    <button type="button" class="btn btn-light-primary btn-sm mb-50" id="btn-clear-search">Clear</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="searchable-tree"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results Column -->
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Search Results</h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div id="search-output">
                                        <p>Search results will appear here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Information Column -->
                    <div class="col-lg-7 col-md-5 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Table Information</h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div id="table-info-panel">
                                        <div id="table-details">
                                            <p>Select a table from the tree or search results to view its data.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End of Row -->
            </div> <!-- End of Container -->
        </div>
    </div>
</div>
<!-- END: Content -->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light navbar-border navbar-shadow">
        <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
            <span class="float-md-left d-block d-md-inline-block">Copyright &copy; 2019 
                <a class="text-bold-800 grey darken-2" href="https://1.envato.market/modern_admin" target="_blank">PIXINVENT</a>
            </span>
            <span class="float-md-right d-none d-lg-block">
                Hand-crafted & Made with <i class="ft-heart pink"></i>
                <span id="scroll-top"></span>
            </span>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/material-vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/extensions/bootstrap-treeview.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/material-app.js"></script>
    <script src="app-assets/js/scripts/extensions/ex-component-tree-views.js"></script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>
