<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xPanel - File Manager</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        /* Ensure breadcrumb and header remain visible */
        .header-navbar, .breadcrumb-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: white;
        }
        .breadcrumb-wrapper {
            margin-top: 60px; /* Adjust based on header height */
        }
        .editor-container {
            margin-top: 120px; /* Space for header and breadcrumb */
            padding: 20px;
            height: calc(100vh - 120px); /* Editor height excluding header and breadcrumb */
            overflow-y: auto;
        }
        .fullscreen .editor-container {
            position: fixed;
            top: 120px; /* Keeps header and breadcrumb visible */
            height: calc(100vh - 120px); /* Fullscreen editor height */
            width: 100%;
            z-index: 999;
        }
        .buttons-below-breadcrumb {
            margin-top: 15px;
        }
    </style>
</head>
<body>

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
<div class="editor-container" id="editor-container">
    <div id="editor">/* PHP/HTML content here */</div>
</div>

<div class="buttons-below-breadcrumb">
    <button id="fullscreen-btn" type="button" class="btn btn-secondary">Toggle Fullscreen</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/php");

    var isFullscreen = false;
    document.getElementById('fullscreen-btn').addEventListener('click', function() {
        var editorContainer = document.getElementById('editor-container');
        if (isFullscreen) {
            editorContainer.classList.remove('fullscreen');
            this.textContent = "Toggle Fullscreen";
        } else {
            editorContainer.classList.add('fullscreen');
            this.textContent = "Exit Fullscreen";
        }
        isFullscreen = !isFullscreen;
        editor.resize();
    });
</script>

</body>
</html>
