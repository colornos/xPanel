<?php
// If the form is submitted, handle the file upload and execution of the bash script
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Directory for storing uploaded files temporarily
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    // Create the uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Call the bash script to upload the file to the remote server
        $output = shell_exec("bash -c './upload.sh " . escapeshellarg($target_file) . " 2>&1'");
        echo "<div class='output'>File uploaded successfully. <br>Server Response: " . nl2br($output) . "</div>";
    } else {
        echo "<div class='output error'>File upload failed.</div>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload to Server</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .output {
            margin-top: 10px;
            color: green;
        }
        .output.error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload File to Remote Server</h1>
        <form id="uploadForm" enctype="multipart/form-data" method="POST">
            <input type="file" name="fileToUpload" id="fileToUpload" required>
            <br>
            <input type="submit" value="Upload File">
        </form>
        <div id="output"></div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('output').innerHTML = data;
            })
            .catch(error => {
                document.getElementById('output').innerHTML = '<div class="output error">Error: ' + error + '</div>';
            });
        });
    </script>
</body>
</html>
