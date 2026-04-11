<?php
session_start();
include 'db.php';
include 'auth.php';

checkPersistentLogin();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$response = ['success' => false, 'message' => ''];

// Helper function to count words in a text file
function countWordsInFile($filePath) {
    $content = file_get_contents($filePath);
    $wordCount = str_word_count($content);
    $lineCount = count(file($filePath));
    return ['words' => $wordCount, 'lines' => $lineCount];
}

// Helper function to check if a date is Friday or Sunday
function isFridayOrSunday($dateString) {
    $date = new DateTime($dateString);
    $dayOfWeek = $date->format('w'); // 0 = Sunday, 5 = Friday
    return $dayOfWeek === '0' || $dayOfWeek === '5';
}

// Helper function to check if submission is allowed for the date
function isSubmissionAllowed($submissionDate) {
    $today = new DateTime();
    $submissionDateTime = new DateTime($submissionDate);
    
    // Get current month and year
    $currentMonth = (int)$today->format('m');
    $currentYear = (int)$today->format('Y');
    $submissionMonth = (int)$submissionDateTime->format('m');
    $submissionYear = (int)$submissionDateTime->format('Y');
    
    // Check if submission is for current or previous month
    $previousMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
    $previousYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;
    
    // Allow current month and previous month
    $isCurrentMonth = ($submissionYear === $currentYear && $submissionMonth === $currentMonth);
    $isPreviousMonth = ($submissionYear === $previousYear && $submissionMonth === $previousMonth);
    
    if (!($isCurrentMonth || $isPreviousMonth)) {
        return false;
    }
    
    // Date cannot be in the future
    if ($submissionDateTime > $today) {
        return false;
    }
    
    return true;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sermonDate = isset($_POST['sermon_date']) ? $_POST['sermon_date'] : '';
    $userId = $_SESSION['user_id'];
    
    // Validate sermon date
    if (empty($sermonDate)) {
        $response['message'] = '❌ Data sermoanei nu a fost specificată.';
        echo json_encode($response);
        exit;
    }
    
    // Validate that date is Friday or Sunday
    if (!isFridayOrSunday($sermonDate)) {
        $response['message'] = '❌ Puteți încărca doar pentru vineri și duminici.';
        echo json_encode($response);
        exit;
    }
    
    // Validate submission is allowed for this date
    if (!isSubmissionAllowed($sermonDate)) {
        $response['message'] = '❌ Puteți încărca doar pentru luna curentă și luna trecută.';
        echo json_encode($response);
        exit;
    }
    
    // Check if file already uploaded for this date
    $checkSql = "SELECT id FROM sermon_submissions WHERE user_id = ? AND sermon_date = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("is", $userId, $sermonDate);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $response['message'] = '❌ Ați trimis deja o notă pentru această dată.';
        echo json_encode($response);
        exit;
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['sermon_file']) || $_FILES['sermon_file']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = '❌ Eroare la încărcarea fișierului. Încercați din nou.';
        echo json_encode($response);
        exit;
    }
    
    $file = $_FILES['sermon_file'];
    $fileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileSize = $file['size'];
    
    // Validate file size (max 10MB)
    if ($fileSize > 10 * 1024 * 1024) {
        $response['message'] = '❌ Fișierul este prea mare. Maximum 10MB.';
        echo json_encode($response);
        exit;
    }
    
    // Validate file is text-based (allow .txt, .pdf, .doc, .docx, etc.)
    $allowedExtensions = ['txt', 'pdf', 'doc', 'docx', 'odt', 'pages', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $allowedMimeTypes = [
        'text/plain',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.oasis.opendocument.text',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpPath);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimeTypes)) {
        $response['message'] = '❌ Tip de fișier necunoscut. Utilizați: txt, pdf, doc, docx, odt, pages, jpg, jpeg, png, gif, webp.';
        echo json_encode($response);
        exit;
    }
    
    // Create upload directory if not exists
    $uploadDir = 'uploads/sermons/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $newFileName = $userId . '_' . str_replace('-', '', $sermonDate) . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (!move_uploaded_file($fileTmpPath, $filePath)) {
        $response['message'] = '❌ Eroare la salvarea fișierului. Contactați administratorul.';
        echo json_encode($response);
        exit;
    }
    
    // Count words and lines (for text files only)
    $wordCount = 0;
    $lineCount = 0;
    if ($fileExtension === 'txt') {
    $counts = countWordsInFile($filePath);
    $wordCount = $counts['words'];
    $lineCount = $counts['lines'];
    } else {
        $wordCount = 0;
        $lineCount = 0;
    }
    
    // Insert into database
    $insertSql = "INSERT INTO sermon_submissions (user_id, sermon_date, file_name, file_path, word_count, line_count, file_size) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("isssiii", $userId, $sermonDate, $fileName, $filePath, $wordCount, $lineCount, $fileSize);
    
    if ($insertStmt->execute()) {
        $response['success'] = true;
        $response['message'] = '✅ Fișierul a fost încărcat cu succes!';
    } else {
        // Delete file if database insert failed
        unlink($filePath);
        $response['message'] = '❌ Eroare la salvarea în bază de date. Contactați administratorul.';
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle file retrieval/view
    $submissionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $userId = $_SESSION['user_id'];
    
    if ($submissionId <= 0) {
        http_response_code(400);
        die(json_encode(['error' => 'Invalid submission ID']));
    }
    
    // Get submission details
    $getSql = "SELECT id, user_id, sermon_date, file_path, file_name FROM sermon_submissions WHERE id = ?";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param("i", $submissionId);
    $getStmt->execute();
    $getResult = $getStmt->get_result();
    
    if ($getResult->num_rows === 0) {
        http_response_code(404);
        die(json_encode(['error' => 'Submission not found']));
    }
    
    $submission = $getResult->fetch_assoc();
    
    // Check if user is owner or admin
    if ($userId !== $submission['user_id'] && $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        die(json_encode(['error' => 'Access denied']));
    }
    
    // Return submission details as JSON
    echo json_encode([
        'id' => $submission['id'],
        'sermon_date' => $submission['sermon_date'],
        'file_name' => $submission['file_name'],
        'file_path' => $submission['file_path']
    ]);
}

echo json_encode($response);
?>
