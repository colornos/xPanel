<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive File Manager with Fullscreen Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .file-list ul {
            padding-left: 0;
            list-style: none;
        }
        .file-list ul li {
            margin: 10px 0;
        }
        #editor {
            border: 1px solid #ccc;
            width: 100%;
            height: calc(100vh - 100px); /* Initially, fill most of the screen */
            transition: height 0.3s ease;
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
        }
        .fullscreen #editor {
            height: 100vh !important;
            width: 100vw !important;
            border: none;
        }
        /* Keep the buttons always visible at the top right in regular and fullscreen mode */
        #button-group {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            gap: 10px;
        }
        /* Ensure no padding/margin in fullscreen */
        .fullscreen-body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="my-4">Responsive File Manager with Fullscreen Editor</h1>

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
                        bodyElement.classList.remove('fullscreen-body');
                        editorElement.classList.remove('fullscreen');
                        this.textContent = "Toggle Fullscreen";
                    } else {
                        bodyElement.classList.add('fullscreen-body');
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
