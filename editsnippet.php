<?php
require 'config/db.php';
require 'includes/auth.php';
$allowed_formats = ['plain', 'HTML', 'CSS', 'javascript', 'python', 'PHP', 'mysql', 'java'];


$errors = [];


$snippet = null;
if (isset($_GET['id'])) {
    $stmt = $connect->prepare("SELECT * FROM snippets WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $snippet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$snippet) {
        header("Location: home.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ? $_POST['title']:'');
    $content = trim($_POST['content'] ?$_POST['content']: '');
    $format = $_POST['format'] ? $_POST['format'] : '';
    $snippet_id = $_GET['id'] ? $_GET['id'] : null;

   
    if (empty($title)) {
        $errors[] = 'Snippet title is required';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must not exceed 255 characters';
    }

    if (empty($content)) {
        $errors[] = 'Snippet content cannot be empty';
    }

    if (!in_array($format, $allowed_formats)) {
        $errors[] = 'Invalid syntax format selected';
    }

    if (empty($errors)) {
        try {
            $stmt = $connect->prepare("UPDATE snippets SET title = ?, content = ?, language = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $format, $snippet_id, $_SESSION['user_id']]);
            $_SESSION['edit_snippet'] = "Updated successfully";
            header("Location: snippets.php");
            exit();
        } catch (PDOException $error) {
            $errors[] = 'Error updating data: ' . $error->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Snippet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/styles/default.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff0f5;
            color: #333;
        }
        header {
            background: #ff5a8a;
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
        }
        .snippet-form {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 30px;
        }
        .form-label {
            color: #ff5a8a;
            font-weight: bold;
        }
        .form-control {
            border: 2px solid #ff7eb3;
            border-radius: 8px;
            padding: 12px;
        }
        .error-alert {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: #ff5a8a;
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
        }
        .btn-primary:hover {
            background: #ff7eb3;
        }
    </style>
</head>
<body>
<header>Edit Snippet</header>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="snippet-form">
                    <h1 class="text-center mb-4">Edit Snippet</h1>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="error-alert">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($snippet): ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Snippet Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?=  htmlspecialchars($snippet['title']) ?>" >
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Snippet Content</label>
                            <textarea class="form-control" id="content" name="content" 
                                      rows="8" ><?= htmlspecialchars($snippet['content']) ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="format" class="form-label">Syntax Format</label>
                            <select class="form-select" id="format" name="format" required>
                                <?php foreach ($allowed_formats as $fmt): ?>
                                    <option value="<?= $fmt ?>" 
                                        <?= ($fmt == $snippet['language']) ? 'selected' : '' ?>>
                                        <?= ucfirst($fmt) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Snippet</button>
                        </div>
                    </form>
                    <?php else: ?>
                        <p class="text-center">Snippet not found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
          hljs.highlightAll();
    </script>
</body>
</html>