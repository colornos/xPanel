<?php
$upload_message = '';

// Handle file upload when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];

        // Define the upload directory
        $upload_dir = __DIR__ . '/uploads/';

        // Ensure the upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Define the destination path
        $destination = $upload_dir . basename($file_name);

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($file_tmp, $destination)) {
            $upload_message = "File uploaded successfully to uploads/" . basename($file_name);
        } else {
            $upload_message = "Error moving the uploaded file.";
        }
    } else {
        $upload_message = "Error uploading file.";
    }
}

// Handle folder download
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    $folder = 'uploads'; // Folder to be zipped and downloaded
    $zip_file = 'uploads.zip';

    // Initialize the ZIP archive
    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        die('Failed to create the ZIP file.');
    }

    // Add files from the folder to the ZIP archive
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::LEAVES_ONLY);
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $file_path = $file->getRealPath();
            $relative_path = substr($file_path, strlen(__DIR__ . '/' . $folder) + 1);
            $zip->addFile($file_path, $relative_path);
        }
    }

    // Close the ZIP archive
    $zip->close();

    // Serve the ZIP file for download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zip_file) . '"');
    header('Content-Length: ' . filesize($zip_file));
    readfile($zip_file);

    // Delete the temporary ZIP file after download
    unlink($zip_file);
    exit;
}
?>

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
    <link rel="stylesheet" href="app-assets/css/material.css">
    <link rel="stylesheet" href="app-assets/css/components.css">
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-compact-menu material-vertical-layout material-layout 1-column fixed-navbar">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-md navbar navbar-with-menu fixed-top navbar-light navbar-shadow navbar-brand-center">
        <div class="navbar-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item"><a class="navbar-brand" href="index.php"><img class="brand-logo" alt="x" src="app-assets/images/logo/logo.png">
                            <h3 class="brand-text">xPanel</h3>
                        </a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-body">
                <!-- File Upload Form -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Upload File</h4>
                    </div>
                    <div class="card-body">
                        <!-- Display upload message -->
                        <?php if ($upload_message): ?>
                            <div class="alert alert-info">
                                <?php echo $upload_message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="uploads.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="file">Choose a file to upload:</label>
                                <input type="file" name="file" id="file" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload File</button>
                        </form>
                    </div>
                </div>

                <!-- Folder Download -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Download Uploads Folder</h4>
                    </div>
                    <div class="card-body">
                        <a href="uploads.php?download=true" class="btn btn-success">Download Uploads Folder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

</body>
<!-- END: Body-->
</html>
