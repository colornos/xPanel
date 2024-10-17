<?php
$dir = "/var/www/html/";

if (isset($_GET['dir'])) {
    $dir = $_GET['dir'];
} else {
    $dir = $dir;
}

// Handle file save operation
if (isset($_POST['file_path']) && isset($_POST['file_content'])) {
    file_put_contents($_POST['file_path'], $_POST['file_content']);
    echo "<p>File saved successfully!</p>";
}

// If a specific file is selected for editing
if (isset($_GET['file'])) {
    $file_path = $_GET['file'];
    $file_content = file_get_contents($file_path);
?>
    <h2>Editing: <?php echo $file_path; ?></h2>
    <form method="POST" action="">
        <input type="hidden" name="file_path" value="<?php echo $file_path; ?>">
        <div id="editor" style="height: 500px; width: 100%;"><?php echo htmlspecialchars($file_content); ?></div>
        <textarea name="file_content" id="file_content" style="display:none;"><?php echo htmlspecialchars($file_content); ?></textarea>
        <br>
        <button type="submit">Save File</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script>
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/php");

        // Update hidden textarea on form submit
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('file_content').value = editor.getValue();
        });
    </script>
<?php
} else {
    // List files and directories
    $files = scandir($dir);

    echo "<h2>File Manager - $dir</h2>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file == "." || $file == "..") continue;

        $file_path = $dir . "/" . $file;

        if (is_dir($file_path)) {
            echo "<li><a href='?dir=$file_path'>$file (Directory)</a></li>";
        } else {
            echo "<li><a href='?file=$file_path'>$file (File)</a></li>";
        }
    }
    echo "</ul>";
}
?>
