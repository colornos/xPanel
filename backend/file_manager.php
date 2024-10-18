<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xPanel - File Manager</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/css/material.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/material-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/material-colors.css">
    <style>
        /* Custom styling for File Manager */
        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }
        .main-content {
            flex: 3;
            margin-right: 20px;
        }
        .section {
            background-color: #fff;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .section-header {
            background-color: #0056A4;
            color: #fff;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
        }
        .file-list ul {
            list-style: none;
            padding-left: 0;
        }
        .file-list ul li {
            padding: 10px;
            margin-bottom: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
        }
        .file-list ul li a {
            text-decoration: none;
        }
        .fullscreen {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .fullscreen #editor {
            height: 100vh !important;
            width: 100vw !important;
            border: none;
        }
    </style>
</head>
<body class="vertical-layout vertical-compact-menu material-vertical-layout material-layout 1-column fixed-navbar">

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
            <div class="navbar-container content">
                <div class="collapse navbar-collapse" id="navbar-mobile">
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="avatar avatar-online"><img src="app-assets/images/portrait/small/avatar-s-19.png" alt="avatar"><i></i></span><span class="user-name"><?php echo $current_user; ?></span></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"><i class="ft-user"></i> Edit Profile</a>
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="ft-power"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-header row">
        <div class="content-header-light col-12">
            <div class="row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <h3 class="content-header-title">File Manager</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">File Manager</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <div class="container">
            <div class="main-content">
                <!-- Files Section -->
                <div class="section">
                    <div class="section-header">Files</div>
                    <div class="file-list">
                        <?php
                        $dir = "/var/www/html/";

                        if (isset($_GET['dir'])) {
                            $dir = $_GET['dir'];
                        }

                        // Handle file save operation
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_path'], $_POST['file_content'])) {
                            if (file_put_contents($_POST['file_path'], $_POST['file_content']) !== false) {
                                echo "<div class='alert alert-success'>File saved successfully!</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Error saving the file!</div>";
                            }
                        }

                        // If a specific file is selected for editing
                        if (isset($_GET['file'])) {
                            $file_path = $_GET['file'];

                            if (file_exists($file_path)) {
                                $file_content = file_get_contents($file_path);
                                ?>
                                <h2>Editing: <?php echo htmlspecialchars($file_path); ?></h2>
                                <form method="POST" action="">
                                    <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($file_path); ?>">
                                    <div id="editor"><?php echo htmlspecialchars($file_content); ?></div>
                                    <textarea name="file_content" id="file_content" style="display:none;"><?php echo htmlspecialchars($file_content); ?></textarea>
                                    <div id="button-group">
                                        <button type="submit" class="btn btn-primary">Save File</button>
                                        <button id="fullscreen-btn" type="button" class="btn btn-secondary">Toggle Fullscreen</button>
                                    </div>
                                </form>

                                <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
                                <script>
                                    var editor = ace.edit("editor");
                                    editor.setTheme("ace/theme/monokai");
                                    editor.session.setMode("ace/mode/php");

                                    // Enable line numbers and word wrap
                                    editor.setOptions({
                                        showLineNumbers: true,
                                        showGutter: true,
                                        wrap: true // Enables word wrapping
                                    });

                                    // Update hidden textarea on form submit
                                    document.querySelector('form').addEventListener('submit', function () {
                                        document.getElementById('file_content').value = editor.getValue();
                                    });

                                    // Set the initial editor height to fill most of the screen
                                    function setEditorHeight() {
                                        var height = window.innerHeight - 100; // Adjust '100' to leave space for header/buttons
                                        document.getElementById('editor').style.height = height + 'px';
                                        editor.resize();
                                    }

                                    setEditorHeight();
                                    window.addEventListener('resize', setEditorHeight);

                                    // Toggle fullscreen mode
                                    var isFullscreen = false;
                                    document.getElementById('fullscreen-btn').addEventListener('click', function() {
                                        var bodyElement = document.body;
                                        var editorElement = document.getElementById('editor');
                                        if (isFullscreen) {
                                            bodyElement.classList.remove('fullscreen');
                                            editorElement.classList.remove('fullscreen');
                                            this.textContent = "Toggle Fullscreen";
                                        } else {
                                            bodyElement.classList.add('fullscreen');
                                            editorElement.classList.add('fullscreen');
                                            this.textContent = "Exit Fullscreen";
                                        }
                                        isFullscreen = !isFullscreen;
                                        editor.resize();
                                    });
                                </script>

                                <?php
                            } else {
                                echo "<div class='alert alert-danger'>File not found!</div>";
                            }
                        } else {
                            // List files and directories
                            $files = scandir($dir);
                            echo "<h2>Current Directory: " . htmlspecialchars($dir) . "</h2>";
                            echo "<nav aria-label='breadcrumb'><ol class='breadcrumb'>";
                            $dir_parts = explode('/', trim($dir, '/'));
                            $path_accum = "/";
                            foreach ($dir_parts as $part) {
                                $path_accum .= $part . "/";
                                echo "<li class='breadcrumb-item'><a href='?dir=$path_accum'>" . htmlspecialchars($part) . "</a></li>";
                            }
                            echo "</ol></nav>";
                            ?>

                            <div class="file-list">
                                <ul class="list-group">
                                    <?php foreach ($files as $file): ?>
                                        <?php if ($file === "." || $file === "..") continue; ?>
                                        <?php $file_path = $dir . "/" . $file; ?>
                                        <li class="list-group-item">
                                            <?php if (is_dir($file_path)): ?>
                                                <a href="?dir=<?php echo urlencode($file_path); ?>" class="text-decoration-none"><?php echo htmlspecialchars($file); ?> (Directory)</a>
                                            <?php else: ?>
                                                <a href="?file=<?php echo urlencode($file_path); ?>" class="text-decoration-none"><?php echo htmlspecialchars($file); ?> (File)</a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<script src="app-assets/vendors/js/material-vendors.min.js"></script>
<script src="app-assets/js/core/app-menu.js"></script>
<script src="app-assets/js/core/app.js"></script>
</body>
</html>
