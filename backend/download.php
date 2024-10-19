<?php
// Handle folder download
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    $folder = 'uploads'; // Folder to be zipped and downloaded
    $zip_file = 'uploads.zip';

    // Ensure the folder exists
    if (!is_dir($folder)) {
        die('The specified folder does not exist.');
    }

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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Download Uploads Folder</title>
    <link rel="stylesheet" href="app-assets/css/material.css">
    <link rel="stylesheet" href="app-assets/css/components.css">
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-compact-menu material-vertical-layout material-layout 1-column fixed-navbar">

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <!-- Folder Download -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Download Uploads Folder</h4>
                    </div>
                    <div class="card-body">
                        <a href="downloads.php?download=true" class="btn btn-success">Download Uploads Folder</a>
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
