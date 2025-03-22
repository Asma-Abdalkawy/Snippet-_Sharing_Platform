-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3310:3310
-- Generation Time: Mar 22, 2025 at 06:48 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `snippets_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `snippets`
--

CREATE TABLE `snippets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `language` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `snippets`
--

INSERT INTO `snippets` (`id`, `user_id`, `title`, `content`, `language`, `created_at`, `update_at`) VALUES
(11, 5, 'simple code', '<form method=\"POST\" action=\"\">\r\n            <input type=\"text\" name=\"Name\" placeholder=\"Full Name\" \r\n                   value=\"<?= htmlspecialchars($user[\'username\']) ?>\" required>\r\n            \r\n            <input type=\"email\" name=\"Email\" placeholder=\"Email\"\r\n                   value=\"<?= htmlspecialchars($user[\'email\']) ?>\" required>\r\n            \r\n            <input type=\"password\" name=\"Password\" placeholder=\"New Password (leave blank to keep current)\">\r\n            \r\n            <input type=\"password\" name=\"confirm_password\" placeholder=\"Confirm New Password\">\r\n            \r\n            <button type=\"submit\">Update Profile</button>\r\n        </form>', 'HTML', '2025-03-21 04:26:17', '2025-03-21 04:46:14'),
(15, 5, 'page code', '<?php\r\nrequire_once \'config/db.php\';\r\nsession_start();\r\n\r\nfunction truncateByLines($content, $lines = 3) {\r\n    $contentLines = explode(\"\\n\", $content);\r\n    if(count($contentLines) > $lines) {\r\n        $truncated = array_slice($contentLines, 0, $lines);\r\n        return [\r\n            \'truncated\' => implode(\"\\n\", $truncated),\r\n            \'full\' => $content,\r\n            \'hasMore\' => true\r\n        ];\r\n    }\r\n    return [\r\n        \'truncated\' => $content,\r\n        \'full\' => $content,\r\n        \'hasMore\' => false\r\n    ];\r\n}\r\n$search = \'\';\r\n$language = \'\';\r\n\r\nif ((!isset($_GET[\'search\']) || !isset($_GET[\'language\']))):\r\n$stmt = $connect->prepare(\"\r\n    SELECT snippets.*, users.username AS author \r\n    FROM snippets\r\n    JOIN users ON snippets.user_id = users.id \r\n    ORDER BY snippets.created_at DESC \r\n\");\r\n$stmt->execute();\r\n$snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);\r\nelse:\r\n    $search = trim(filter_input(INPUT_GET, \'search\', FILTER_SANITIZE_STRING));\r\n    $language = trim(filter_input(INPUT_GET, \'language\', FILTER_SANITIZE_STRING));\r\n        // البحث \r\n        $sql = \"SELECT snippets.*, users.username AS author \r\n                        FROM snippets\r\n                        JOIN users ON snippets.user_id = users.id \r\n                        WHERE \";\r\n        $params = [];\r\n        if(!empty($language)&& $language!=\'all\' && !empty($search)){\r\n            $params[] = \"%$language%\";\r\n            $params[] = \"%$search%\";\r\n            $sql=$sql.\"title LIKE ? or language LIKE ?\";\r\n        }\r\n        elseif(!empty($language) && $language!=\'all\'){\r\n            $params[] = \"%$language%\";\r\n            $sql=$sql.\"language LIKE ?\";\r\n        }\r\n        else{\r\n            $params[] = \"%$search%\";\r\n            $sql=$sql.\"title LIKE ?\";\r\n        }\r\n        $sql=$sql.\"ORDER BY created_at DESC\";\r\n        // تنفيذ الاستعلام\r\n        $stmt = $connect->prepare($sql);\r\n        $stmt->execute($params);\r\n        $snippets = $stmt->fetchAll(PDO::FETCH_ASSOC); \r\n        \r\nendif;\r\n?>\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Snippet Sharing Platform</title>\r\n    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">\r\n    <link rel=\"stylesheet\" href=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/styles/default.min.css\">\r\n    <style>\r\n        body {\r\n            font-family: Arial, sans-serif;\r\n            background: #fff0f5;\r\n            color: #333;\r\n        }\r\n        header {\r\n            background: #ff5a8a;\r\n            color: white;\r\n            padding: 1rem;\r\n            text-align: center;\r\n            font-size: 1.5rem;\r\n        }\r\n        nav {\r\n            background: #ff7eb3;\r\n            padding: 1rem;\r\n            display: flex;\r\n            justify-content: center;\r\n            gap: 2rem;\r\n        }\r\n        nav a {\r\n            color: white;\r\n            text-decoration: none;\r\n            font-weight: bold;\r\n        }\r\n        .container {\r\n            max-width: 1000px;\r\n            margin: 2rem auto;\r\n            padding: 0 1rem;\r\n        }\r\n        .snippet {\r\n            background: white;\r\n            border-radius: 8px;\r\n            box-shadow: 0 2px 4px rgba(0,0,0,0.1);\r\n            margin-bottom: 1.5rem;\r\n            padding: 1.5rem;\r\n        }\r\n        .snippet h3 {\r\n            color: #ff5a8a;\r\n            margin-bottom: 0.5rem;\r\n        }\r\n        .meta {\r\n            color: #666;\r\n            font-size: 0.9rem;\r\n            margin-bottom: 1rem;\r\n        }\r\n        pre {\r\n            background: #ffe6e9;\r\n            padding: 1rem;\r\n            border-radius: 6px;\r\n            white-space: pre-wrap;\r\n            word-break: break-all;\r\n            margin: 1rem 0;\r\n        }\r\n        .snippet-actions {\r\n            display: flex;\r\n            gap: 0.5rem;\r\n            margin-top: 1rem;\r\n        }\r\n        button {\r\n            padding: 0.5rem 1rem;\r\n            border: none;\r\n            border-radius: 4px;\r\n            cursor: pointer;\r\n            transition: opacity 0.2s;\r\n        }\r\n        button:hover {\r\n            opacity: 0.8;\r\n        }\r\n        .btn-primary {\r\n            background: #ff5a8a;\r\n            color: white;\r\n        }\r\n        .btn-secondary {\r\n            background: #ff7eb3;\r\n            color: white;\r\n        }\r\n        .btn-copy {\r\n    background:rgb(175, 153, 168); \r\n    color: white;\r\n}\r\n        .error-message {\r\n    color: red;\r\n    margin: 10px 0;\r\n    padding: 10px;\r\n    background: #ffe6e6;\r\n    border-radius: 5px;\r\n}\r\n.success-message{\r\n    color: green;\r\n    margin: 10px 0;\r\n    padding: 10px;\r\n    background:rgb(230, 255, 235);\r\n    border-radius: 5px;\r\n}\r\n.btn {\r\n            background: #ff5a8a;\r\n            border: none;\r\n            padding: 12px 25px;\r\n            font-size: 1.1rem;\r\n        }\r\n .btn:hover {\r\n            background: #ff7eb3;\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <header><b>Snippets</b></h3></header>\r\n    <nav>\r\n        <?php if(isset($_SESSION[\'user_id\'])): ?>\r\n            <a href=\"home.php\">Home</a>\r\n            <a href=\"create.php\">Create snippet</a>\r\n            <a href=\"logout.php\">Logout</a>\r\n        <?php endif; ?>\r\n    </nav>\r\n\r\n    <div class=\"container\">\r\n        <?php if(isset($_SESSION[\'user_id\'])): ?>\r\n            <div class=\"search-bar\" style=\"margin-bottom: 2rem;\">\r\n            <form method=\"GET\" class=\"d-flex gap-2\">\r\n    <input type=\"text\" \r\n           name=\"search\" \r\n           class=\"flex-grow-1\" \r\n           placeholder=\"Search by title\"\r\n           value=\"<?= htmlspecialchars($search) ?>\">\r\n           \r\n    <select name=\"language\" class=\"form-select\">\r\n        <option value=\"all\">All Languages</option>\r\n        <?php \r\n        $languages = [\'php\', \'javascript\', \'python\', \'html\', \'css\', \'sql\'];\r\n        foreach ($languages as $lang): ?>\r\n            <option value=\"<?= $lang ?>\" \r\n                <?= ($language === $lang) ? \'selected\' : \'\' ?>>\r\n                <?= ucfirst($lang) ?>\r\n            </option>\r\n        <?php endforeach; ?>\r\n    </select>\r\n    \r\n    <button type=\"submit\" class=\"btn-primary\">Search</button>\r\n</form>\r\n            </div>\r\n        <?php endif; ?>\r\n\r\n        <?php if (empty($snippets)): ?>\r\n            <div class=\"error-message\">\r\n                    <p>NO snippet found!</p>\r\n            </div>\r\n        <?php endif; ?>\r\n\r\n        <?php if (isset( $_SESSION[\'create_messge\'])): ?>\r\n            <div class=\"success-message\">\r\n                    <p><?php echo  $_SESSION[\'create_messge\'];?></p>\r\n            </div>\r\n            <?php unset($_SESSION[\'create_messge\']);?>\r\n        <?php endif; ?>\r\n\r\n        <?php if (isset( $_SESSION[\'delete_messge\'])): ?>\r\n            <div class=\"success-message\">\r\n                    <p><?php echo  $_SESSION[\'delete_messge\'];?></p>\r\n            </div>\r\n            <?php unset($_SESSION[\'delete_messge\']);?>\r\n        <?php endif; ?>\r\n        \r\n        <?php if (isset( $_SESSION[\'edit_snippet\'])): ?>\r\n            <div class=\"success-message\">\r\n                    <p><?php echo  $_SESSION[\'edit_snippet\'];?></p>\r\n            </div>\r\n            <?php unset($_SESSION[\'edit_snippet\']);?>\r\n        <?php endif; ?>\r\n        <div class=\"snippets-list\">\r\n            <?php foreach($snippets as $snippet): \r\n                $processed = truncateByLines($snippet[\'content\']);\r\n            ?>\r\n                <div class=\"snippet\">\r\n                    <h3><?= ucfirst(htmlspecialchars($snippet[\'title\']))?></h3>\r\n                    <div class=\"meta\">\r\n                        <span>Author: <?=ucfirst(htmlspecialchars($snippet[\'author\']))?></span> |\r\n                        <span>Language: <?= htmlspecialchars($snippet[\'language\']) ?></span> |\r\n                        <?php if($snippet[\'update_at\']==null): ?>\r\n                        <span><?= date(\'M d, Y\', strtotime($snippet[\'created_at\'])) ?></span>\r\n                        <?php else: ?>\r\n                            <span><?= date(\'M d, Y\', strtotime($snippet[\'update_at\'])) ?></span>\r\n                            <?php endif; ?>\r\n                    </div>\r\n                    \r\n                    <pre><code class=\"language-<?= htmlspecialchars($snippet[\'language\']) ?>\"\r\n                              data-truncated=\"<?= htmlspecialchars($processed[\'truncated\']) ?>\"\r\n                              data-full=\"<?= htmlspecialchars($processed[\'full\']) ?>\">\r\n<?= htmlspecialchars($processed[\'truncated\']) ?></code></pre>\r\n\r\n                    <div class=\"snippet-actions\">\r\n                        <?php if(isset($_SESSION[\'user_id\'])&& $_SESSION[\'user_id\'] == $snippet[\'user_id\']): ?>\r\n                            <a href=\"editsnippet.php?id=<?= $snippet[\'id\'] ?>\" class=\"btn btn-primary\">Edit</a>\r\n                            <form method=\"POST\" action=\"delete.php\" class=\"d-inline\" onsubmit=\"return confirmDelete()\">\r\n                            <input type=\"hidden\" name=\"snippet_id\" value=\"<?= $snippet[\'id\'] ?>\">\r\n                            <button type=\"submit\" class=\"btn btn-danger\">Delete</button>\r\n                          </form>\r\n                          <?php endif; ?>\r\n                          <?php if(isset($_SESSION[\'user_id\'])): ?>\r\n                          <button class=\"copy-btn\">Copy</button>\r\n                          <?php endif; ?>\r\n                        <?php if($processed[\'hasMore\']): ?>\r\n                            <button class=\"btn-secondary read-more\">Read More</button>\r\n                        <?php endif; ?>\r\n                    </div>\r\n                </div>\r\n            <?php endforeach; ?>\r\n        </div>\r\n    </div>\r\n\r\n    <script src=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/highlight.min.js\"></script>\r\n    <script>\r\n        // Initialize syntax highlighting\r\n        hljs.highlightAll();\r\n        //delet message\r\n        function confirmDelete() {\r\n    return confirm(\'Are you sure you want to delete this snippet?\');\r\n}\r\ndocument.addEventListener(\'DOMContentLoaded\', function() {\r\n    const deleteMessage = document.getElementById(\'deleteMessage\');\r\n    if (deleteMessage) {\r\n        setTimeout(() => {\r\n            deleteMessage.style.display = \'none\';\r\n        }, 3000); // 3000 مللي ثانية = 3 ثواني\r\n    }\r\n});\r\n        \r\n        // Read More functionality\r\n        document.querySelectorAll(\'.read-more\').forEach(button => {\r\n            button.addEventListener(\'click\', (e) => {\r\n                const snippet = e.target.closest(\'.snippet\');\r\n                const code = snippet.querySelector(\'code\');\r\n                const isExpanded = code.textContent.trim() === code.dataset.full;\r\n                \r\n                code.textContent = isExpanded ? code.dataset.truncated : code.dataset.full;\r\n                e.target.textContent = isExpanded ? \'Read More\' : \'Show Less\';\r\n                \r\n                // Re-apply syntax highlighting\r\n                hljs.highlightElement(code);\r\n            });\r\n        });\r\n\r\n        // \r\ndocument.querySelectorAll(\'.copy-btn\').forEach(button => {\r\n    button.addEventListener(\'click\', () => {\r\n        const snippet = button.closest(\'.snippet\');\r\n        const code = snippet.querySelector(\'code\');\r\n        const content = code.dataset.full;\r\n        navigator.clipboard.writeText(content)\r\n            .then(() => {\r\n                alert(\'Copied to clipboard!\');\r\n            })\r\n            .catch(err => {\r\n                console.error(\'Copy failed:\', err);\r\n            });\r\n    });\r\n});\r\n    </script>\r\n</body>\r\n</html>', 'PHP', '2025-03-21 22:10:47', '2025-03-22 02:36:22'),
(19, 4, 'simple html code', '<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Document</title>\r\n</head>\r\n<body>\r\n    <h1>hello world!</h1>\r\n</body>\r\n</html>', 'html', '2025-03-22 05:41:15', '2025-03-22 05:41:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `update_at`) VALUES
(4, 'mindset', 'mindset@info.com', '$2y$10$GCcBhTZzzc0BSQ2sUxQo7..HnWJvXB15jiF5rZ5Tkxdt4pXhFxGha', '2025-03-20 18:59:26', '2025-03-22 05:39:42'),
(5, 'asma', 'asma@info.com', '$2y$10$PM26lya77MksbJwGUeCvduC2jU4gJ6Inzw21VnHMzU4PqodwJvph6', '2025-03-20 21:57:28', '2025-03-21 23:05:26'),
(6, 'mindset', 'mindset@gmail.com', '$2y$10$OZh9vHy8XOjMfGWo4VV7MO.sposzR6B1AUMzMMUKErfUQic8k/MDu', '2025-03-21 19:47:37', '2025-03-21 19:47:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `snippets`
--
ALTER TABLE `snippets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `snippets`
--
ALTER TABLE `snippets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `snippets`
--
ALTER TABLE `snippets`
  ADD CONSTRAINT `snippets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
