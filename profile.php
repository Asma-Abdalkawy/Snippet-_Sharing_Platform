<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

function truncateByLines($content, $lines = 3) {
    $contentLines = explode("\n", $content);
    if(count($contentLines) > $lines) {
        $truncated = array_slice($contentLines, 0, $lines);
        return [
            'truncated' => implode("\n", $truncated),
            'full' => $content,
            'hasMore' => true
        ];
    }
    return [
        'truncated' => $content,
        'full' => $content,
        'hasMore' => false
    ];
}
$search = '';
$language = '';
$user_id=$_SESSION['user_id'];

if ((!isset($_GET['search']) || !isset($_GET['language']))):
$stmt = $connect->prepare("
    SELECT snippets.*, users.username as author ,users.email as author_email 
    FROM snippets
    JOIN users ON snippets.user_id = users.id 
    WHERE user_id=?;
    ORDER BY snippets.created_at DESC 
");
$stmt->execute([$user_id]);
$snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);
else:
    $search = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING));
    $language = trim(filter_input(INPUT_GET, 'language', FILTER_SANITIZE_STRING));
        // البحث 
        $sql = "SELECT snippets.*, users.username as author ,users.email as author_email 
                        FROM snippets
                        JOIN users ON snippets.user_id = users.id 
                        WHERE ";
        $params = [];
        if(!empty($language)&& $language!='all' && !empty($search)){
            $params[] = "%$language%";
            $params[] = "%$search%";
            $sql=$sql."title LIKE ? or language LIKE ?";
        }
        elseif(!empty($language) && $language!='all'){
            $params[] = "%$language%";
            $sql=$sql."language LIKE ?";
        }
        else{
            $params[] = "%$search%";
            $sql=$sql."title LIKE ?";
        }
        $sql=$sql." AND user_id=? ORDER BY created_at DESC";
        $params[] =$user_id;
        $stmt = $connect->prepare($sql);
        $stmt->execute($params);
        $snippets = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        
endif;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snippet Sharing Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/styles/default.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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
        nav {
            background: #ff7eb3;
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .p_container {
            max-width: 950px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .user-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .snippet {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }
        .snippet h3 {
            color: #ff5a8a;
            margin-bottom: 0.5rem;
        }
        .meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        pre {
            background: #ffe6e9;
            padding: 1rem;
            border-radius: 6px;
            white-space: pre-wrap;
            word-break: break-all;
            margin: 1rem 0;
        }
        .snippet-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
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
        .error-message {
    color: red;
    margin: 10px 0;
    padding: 10px;
    background: #ffe6e6;
    border-radius: 5px;
}
.success-message{
    color: green;
    margin: 10px 0;
    padding: 10px;
    background:rgb(230, 255, 235);
    border-radius: 5px;
}
.btn {
            background: #ff5a8a;
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
        }
 .btn:hover {
            background: #ff7eb3;
        }
    </style>
</head>
<body>
<header>My Profile</header>
    <nav>
            <a href="home.php">Home</a>
            <a href="logout.php">Logout</a>
    </nav>
    <div class="p_container">
        <div class="user-info">
            <?php if (isset( $_SESSION['edit_profile'])): ?>
                    <div class="success-message">
                            <p><?php echo  $_SESSION['edit_profile'];?></p>
                    </div>
                    <?php unset($_SESSION['edit_profile']);?>
                <?php endif; ?>
            <?php
            $sql="SELECT username as author, email as author_email from users where id=?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC); 
            ?>
            
            <h2><?=ucfirst(htmlspecialchars($user['author'])) ?></h2>
            <p><?=ucfirst(htmlspecialchars($user['author_email'])) ?></p>
            <a href="editprofile.php?id=<?= $_SESSION['user_id'] ?>" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>

    <div class="container">
       
            <div class="search-bar" style="margin-bottom: 2rem;">
            <form method="GET" class="d-flex gap-2">
    <input type="text" 
           name="search" 
           class="flex-grow-1" 
           placeholder="Search by title"
           value="<?= htmlspecialchars($search) ?>">
           
    <select name="language" class="form-select">
        <option value="all">All Languages</option>
        <?php 
        $languages = ['PHP', 'javascript', 'python', 'HTML', 'CSS', 'sql'];
        foreach ($languages as $lang): ?>
            <option value="<?= $lang ?>" 
                <?= ($language === $lang) ? 'selected' : '' ?>>
                <?= ucfirst($lang) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-primary">Search</button>
</form>
            </div>
    

        <?php if (empty($snippets)): ?>
            <div class="error-message">
                    <p>NO snippet found!</p>
            </div>
        <?php endif; ?>
        <?php if (isset( $_SESSION['delete_messge'])): ?>
            <div class="success-message">
                    <p><?php echo  $_SESSION['delete_messge'];?></p>
            </div>
            <?php unset($_SESSION['delete_messge']);?>
        <?php endif; ?>
    

        <div class="snippets-list">
            <?php foreach($snippets as $snippet): 
                $processed = truncateByLines($snippet['content']);
            ?>
                <div class="snippet">
                    <h3><?=ucfirst(htmlspecialchars($snippet['title']))?></h3>
                    <div class="meta">
                        <span>Language: <?= htmlspecialchars($snippet['language']) ?></span> |
                        <span><?= date('M d, Y', strtotime($snippet['created_at'])) ?></span>
                    </div>
                    
                    <pre><code class="language-<?= htmlspecialchars($snippet['language']) ?>"
                              data-truncated="<?= htmlspecialchars($processed['truncated']) ?>"
                              data-full="<?= htmlspecialchars($processed['full']) ?>">
<?= htmlspecialchars($processed['truncated']) ?></code></pre>

                    <div class="snippet-actions">
                            <a href="editsnippet.php?id=<?= $snippet['id'] ?>" class="btn btn-primary">Edit</a>
                            <form method="POST" action="delete.php" class="d-inline" onsubmit="return confirmDelete()">
                            <input type="hidden" name="snippet_id" value="<?= $snippet['id'] ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                          </form>
                        
                        <?php if($processed['hasMore']): ?>
                            <button class="btn-secondary read-more">Read More</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/highlight.min.js"></script>
    <script>
        // Initialize syntax highlighting
        hljs.highlightAll();
        //delet message
        function confirmDelete() {
    return confirm('Are you sure you want to delete this snippet?');
}
document.addEventListener('DOMContentLoaded', function() {
    const deleteMessage = document.getElementById('deleteMessage');
    if (deleteMessage) {
        setTimeout(() => {
            deleteMessage.style.display = 'none';
        }, 3000); // 3000 مللي ثانية = 3 ثواني
    }
});
        
        // Read More functionality
        document.querySelectorAll('.read-more').forEach(button => {
            button.addEventListener('click', (e) => {
                const snippet = e.target.closest('.snippet');
                const code = snippet.querySelector('code');
                const isExpanded = code.textContent.trim() === code.dataset.full;
                
                code.textContent = isExpanded ? code.dataset.truncated : code.dataset.full;
                e.target.textContent = isExpanded ? 'Read More' : 'Show Less';
                
                // Re-apply syntax highlighting
                hljs.highlightElement(code);
            });
        });
    </script>
</body>
</html>