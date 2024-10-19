<?php
$upload_dir = 'uploads/';

// Handle file upload when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['files'])) {
        $total_files = count($_FILES['files']['name']);
        $upload_message = "";

        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['files']['name'][$i];
            $file_tmp = $_FILES['files']['tmp_name'][$i];
            $file_path = $_POST['file_paths'][$i];

            // Ensure the subdirectories exist
            $sub_dir = $upload_dir . dirname($file_path);
            if (!is_dir($sub_dir)) {
                mkdir($sub_dir, 0777, true);
            }

            // Define the destination path
            $destination = $upload_dir . $file_path;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($file_tmp, $destination)) {
                $upload_message .= "File '{$file_name}' uploaded successfully to '{$destination}'.<br>";
            } else {
                $upload_message .= "Error uploading file '{$file_name}'.<br>";
            }
        }
    } else {
        $upload_message = "No files were uploaded.";
    }
}

// Get the list of uploaded files
function listFiles($dir) {
    $files = array();
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $files[$item] = listFiles($path);
        } else {
            $files[] = $item;
        }
    }
    return $files;
}

$files = listFiles($upload_dir);
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
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/material-icons/material-icons.css">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/material-vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/ui/prism.min.css">
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
    <!-- END: Page CSS-->
    
    <!-- Original Styling -->
    <style>
.dropzone {
    border: 2px dashed #007bff;
    border-radius: 5px;
    padding: 50px; /* Increase padding for more internal space */
    text-align: center;
    color: #007bff;
    cursor: pointer;
    width: 100%; /* Expand width to fit the container */
    min-height: 300px; /* Set a larger minimum height */
}
        .file-preview {
            margin-top: 15px;
        }
        .tree ul {
            list-style: none;
            margin-left: 20px;
            padding-left: 20px;
            border-left: 1px dashed #ccc;
        }
        .tree li {
            margin: 5px 0;
            padding-left: 15px;
        }
        .tree li.file > span:before {
            content: '📄 ';
        }
        .tree li.folder > span:before {
            content: '📁 ';
        }
        .tree li ul {
            display: none;
        }
        .tree li.open > ul {
            display: block;
        }
    </style>
</head>

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

    <div class="app-content content"> <!-- Adjusted margin to avoid header overlap -->
        <div class="content-wrapper">
            <div class="content-body">
                <!-- File Upload Form -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Upload Folders and Multiple Files</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($upload_message)): ?>
                            <div class="alert alert-info">
                                <?php echo $upload_message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Drag-and-Drop Area -->
                        <div id="dropzone" class="dropzone">
                            Drag and drop folders or files here or click to select
                        </div>

                        <!-- Hidden File Input -->
                        <form action="" method="POST" enctype="multipart/form-data" id="upload-form">
                            <input type="file" name="files[]" id="file-input" class="d-none" multiple>
                            <div id="hidden-inputs"></div>
                            <div class="file-preview tree" id="file-preview"></div>
                            <button type="submit" class="btn btn-primary mt-2">Upload Files</button>
                        </form>
                    </div>
                </div>

                <!-- File Tree View for Uploaded Files -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Uploaded Files</h4>
                    </div>
                    <div class="card-body">
                        <div class="tree">
                            <ul>
                                <?php function renderTree($files) {
                                    foreach ($files as $key => $value) {
                                        if (is_array($value)) {
                                            echo "<li class='folder'><span>{$key}</span><ul>";
                                            renderTree($value);
                                            echo "</ul></li>";
                                        } else {
                                            echo "<li class='file'><span><a href='uploads/{$value}' target='_blank'>{$value}</a></span></li>";
                                        }
                                    }
                                }
                                renderTree($files);
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drag-and-Drop Script with Folder Structure Preservation -->
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file-input');
        const filePreview = document.getElementById('file-preview');
        const hiddenInputs = document.getElementById('hidden-inputs');
        let selectedFiles = [];
        let filePaths = [];

        // Handle drag-and-drop events
        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
        dropzone.addEventListener('drop', async (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');

            const items = e.dataTransfer.items;
            for (let i = 0; i < items.length; i++) {
                const entry = items[i].webkitGetAsEntry();
                if (entry) {
                    await traverseFileTree(entry);
                }
            }
            updateFilePreview();
            updateHiddenInputs();
        });

        // Traverse directory and add files recursively
        async function traverseFileTree(entry, path = "") {
            if (entry.isFile) {
                await new Promise((resolve) => {
                    entry.file(file => {
                        selectedFiles.push(file);
                        filePaths.push(path + file.name);
                        resolve();
                    });
                });
            } else if (entry.isDirectory) {
                const reader = entry.createReader();
                let entries = [];

                // Read all entries
                const readEntries = async () => {
                    return new Promise((resolve) => {
                        reader.readEntries((results) => {
                            if (results.length) {
                                entries = entries.concat(results);
                                resolve(readEntries());
                            } else {
                                resolve(entries);
                            }
                        });
                    });
                };

                entries = await readEntries();
                for (let i = 0; i < entries.length; i++) {
                    await traverseFileTree(entries[i], path + entry.name + "/");
                }
            }
        }

        // Update file preview
        function updateFilePreview() {
            filePreview.innerHTML = '<ul>' + generateTreeHTML(filePaths) + '</ul>';
        }

        // Generate tree structure for preview
        function generateTreeHTML(paths) {
            const root = {};
            paths.forEach(path => {
                const parts = path.split('/');
                let current = root;
                parts.forEach((part, index) => {
                    if (!current[part]) {
                        current[part] = (index === parts.length - 1) ? 'file' : {};
                    }
                    current = current[part];
                });
            });

            return renderTreeHTML(root);
        }

        // Render tree HTML recursively
        function renderTreeHTML(node) {
            let html = '';
            for (const key in node) {
                if (node[key] === 'file') {
                    html += `<li class='file'><span>${key}</span></li>`;
                } else {
                    html += `<li class='folder'><span>${key}</span><ul>${renderTreeHTML(node[key])}</ul></li>`;
                }
            }
            return html;
        }

        // Update hidden inputs for each file path
        function updateHiddenInputs() {
            hiddenInputs.innerHTML = '';
            filePaths.forEach((path) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'file_paths[]';
                input.value = path;
                hiddenInputs.appendChild(input);
            });

            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }

        // Handle file input change
        fileInput.addEventListener('change', () => {
            for (let i = 0; i < fileInput.files.length; i++) {
                const file = fileInput.files[i];
                selectedFiles.push(file);
                filePaths.push(file.webkitRelativePath || file.name);
            }
            updateFilePreview();
            updateHiddenInputs();
        });

        // Add click event to folder elements for toggling
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.tree li.folder > span').forEach(folder => {
                folder.addEventListener('click', function() {
                    const parentLi = this.parentElement;
                    parentLi.classList.toggle('open'); // Toggle 'open' class to show/hide files
                });
            });
        });
    </script>
</body>
</html>
