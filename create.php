<?php
require 'config/db.php';
require 'includes/auth.php';
$errors=[];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content =trim($_POST['content']);
    $format = $_POST['format'];
    $user_id = $_SESSION['user_id'];
    if (empty($title) && empty($content)) {
        $errors[] = 'title and contant are required';
    }
    elseif (empty($title)) {
        $errors[] = 'Snippet title is required';
    } elseif (empty($content)) {
        $errors[] = 'Snippet content cannot be empty';
    }
    if (strlen($title) > 255) {
        $errors[] = 'Title must not exceed 255 characters';
    }
    
    if(empty($errors)){
        try {
            $stmt = $connect->prepare("INSERT INTO snippets (user_id, title,content,language) VALUES (?, ?, ?,?)");
            $stmt->execute([$user_id,$title ,$content, $format]);
            $_SESSION['create_messge'] = "Snippet created successfully";
            header("Location: snippets.php?id=" . $connect->lastInsertId());
            exit();
        } catch (PDOException $error) {
            $errors[] = 'Error saving data: ' . $error->getMessage();   
        }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Snippet</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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
            padding: 20px;
            margin-top: 20px;
        }
        .snippet-form textarea {
            min-height: 300px;
        }
        .text-center{
            color: #ff5a8a;

        }
        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        button:hover {
            opacity: 0.8;
        }
        .btn-primary {
            background: #ff5a8a;
            color: white;
        }
        .btn-secondary {
            background: #ff7eb3;
            color: white;
        }
        .btn-primary {
            background: #ff5a8a;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: opacity 0.2s;
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
            border: 2px solidrgb(70, 38, 52);
            border-radius: 8px;
            padding: 12px;
        }
        .error-message {
    color: red;
    margin: 10px 0;
    padding: 10px;
    background: #ffe6e6;
    border-radius: 5px;
}

    </style>
</head>
<body>
<header></header>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="snippet-form">
                    <h1 class="text-center mb-4">Create New snippet</h1>
                    <?php if (!empty($errors)): ?>
                        <div class="error-message">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-1"><?= ucfirst(htmlspecialchars($error)) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="post">
                    <div class="mb-3">
                      <label for="title" class="form-label">Snippet Title</label>
                      <input type="text" class="form-control" id="title" name="title" >
                    </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">snippet Content</label>
                            <textarea class="form-control" id="content" name="content" ></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="format" class="form-label">Syntax Format</label>
                            <select class="form-select" id="format" name="format">
                                <option value="plain">Plain Text</option>
                                <option value="html">HTML</option>
                                <option value="css">CSS</option>
                                <option value="javascript">JavaScript</option>
                                <option value="python">Python</option>
                                <option value="php">PHP</option>
                                <option value="mysql">MySQL</option>
                                <option value="java">Java</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn-primary">Create snippet</button>
                            <a href="home.php" class="btn btn-primary" style="border:none">Back to home</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>