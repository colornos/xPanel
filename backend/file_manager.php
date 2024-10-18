<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive File Manager with Fullscreen Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 20px;
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
            height: 500px;
            width: 100%;
            margin-bottom: 15px;
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
        }
        #fullscreen-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10000;
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
                <button type="submit" class="btn btn-primary mt-3">Save File</button>
            </form>
            <button id="fullscreen-btn" class="btn btn-secondary">Toggle Fullscreen</button>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
            <script>
                var editor = ace.edit("editor");
                editor.setTheme("ace/theme/monokai");
                editor.session.setMode("ace/mode/php");

                // Update hidden textarea on form submit
                document.querySelector('form').addEventListener('submit', function () {
                    document.getElementById('file_content').value = editor.getValue();
                });

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
