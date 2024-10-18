<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xPanel - File Manager</title>
    <!-- Bootstrap 4 for styling and breadcrumbs -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
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
            position: fixed;
            top: 120px; /* Account for header and breadcrumb */
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            overflow: hidden;
            height: calc(100vh - 120px);
        }
        .fullscreen #editor {
            height: calc(100vh - 120px); /* Leaves space for the header and breadcrumb */
            width: 100%;
        }
        .buttons-below-breadcrumb {
            margin-top: 15px;
        }
        .breadcrumb-wrapper, .header-navbar {
            position: fixed;
            width: 100%;
            z-index: 1000;
            background-color: white;
        }
        .header-navbar {
            top: 0;
        }
        .breadcrumb-wrapper {
            top: 60px; /* Space for header */
        }
        .content-wrapper {
            margin-top: 120px; /* Space for header and breadcrumb */
        }
        .buttons-top-right {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
    </style>
</head>
<body class="vertical-layout vertical-compact-menu material-vertical-layout material-layout 1-column fixed-navbar">

<!-- BEGIN: Header-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-light navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item"><a class="navbar-brand" href="index.php"><img class="brand-logo" alt="x" src="app-assets/images/logo/logo.png">
                    <h3 class="brand-text">xPanel</h3></a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- END: Header-->

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">File Manager</li>
        </ol>
    </nav>
</div>

<!-- Main Content -->
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
                            <form id="file-form" method="POST" action="">
                                <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($file_path); ?>">
                                <div id="editor"><?php echo htmlspecialchars($file_content); ?></div>
                                <textarea name="file_content" id="file_content" style="display:none;"><?php echo htmlspecialchars($file_content); ?></textarea>
                            </form>

                            <!-- Buttons show only when a file is selected -->
                            <div class="buttons-below-breadcrumb">
                                <button type="submit" form="file-form" class="btn btn-primary">Save File</button>
                                <button id="fullscreen-btn" type="button" class="btn btn-secondary">Toggle Fullscreen</button>
                            </div>

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
                                    var height = window.innerHeight - 120; // Leaves space for header and breadcrumb
                                    document.getElementById('editor').style.height = height + 'px';
                                    editor.resize();
                                }

                                setEditorHeight();
                                window.addEventListener('resize', setEditorHeight);

                                // Toggle fullscreen mode
                                var isFullscreen = false;
                                document.getElementById('fullscreen-btn').addEventListener('click', function() {
                                    var editorElement = document.getElementById('editor');
                                    if (isFullscreen) {
                                        editorElement.classList.remove('fullscreen');
                                        this.textContent = "Toggle Fullscreen";
                                    } else {
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
<!-- END: Content-->

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
