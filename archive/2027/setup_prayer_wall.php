<?php
/**
 * Prayer Wall Database Setup
 * Creates the necessary tables for the prayer wall feature
 * Run this script once to initialize the database
 */

include 'db.php';

$tables_created = [];
$errors = [];

// 1. Create prayer_requests table
$sql_prayer_requests = "
CREATE TABLE IF NOT EXISTS prayer_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category ENUM('lauda', 'multumire', 'cerere', 'mijlocire', 'marturisire') NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_prayer_requests)) {
    $tables_created[] = 'prayer_requests';
} else {
    $errors[] = 'Error creating prayer_requests table: ' . $conn->error;
}

// 2. Create prayer_reactions table (emoticon reactions)
$sql_reactions = "
CREATE TABLE IF NOT EXISTS prayer_reactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prayer_id INT NOT NULL,
    user_id INT NOT NULL,
    emoticon VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prayer_id) REFERENCES prayer_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_prayer (prayer_id, user_id),
    INDEX idx_prayer_id (prayer_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_reactions)) {
    $tables_created[] = 'prayer_reactions';
} else {
    $errors[] = 'Error creating prayer_reactions table: ' . $conn->error;
}

// 3. Create prayer_praying table (tracking "praying for you")
$sql_praying = "
CREATE TABLE IF NOT EXISTS prayer_praying (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prayer_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prayer_id) REFERENCES prayer_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_praying (prayer_id, user_id),
    INDEX idx_prayer_id (prayer_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_praying)) {
    $tables_created[] = 'prayer_praying';
} else {
    $errors[] = 'Error creating prayer_praying table: ' . $conn->error;
}

// Output results
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prayer Wall Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Prayer Wall Database Setup</h1>
        
        <?php if (!empty($tables_created)): ?>
            <div class="alert alert-success">
                <h4>Tables Created Successfully</h4>
                <ul>
                    <?php foreach ($tables_created as $table): ?>
                        <li><strong><?= htmlspecialchars($table) ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h4>Errors Encountered</h4>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (empty($errors) && !empty($tables_created)): ?>
            <div class="alert alert-info">
                <p>Setup complete! You can now <a href="pray.php">go to the prayer wall</a>.</p>
                <p><small>You can safely delete this file (setup_prayer_wall.php) after setup is complete.</small></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
