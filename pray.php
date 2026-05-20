<?php
/**
 * Prayer Wall Page
 * /pray.php
 */

include 'db.php';
include 'auth.php';

session_start();
checkPersistentLogin();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'] ?? 'User';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prayer Wall</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="pray_styles.css">
</head>
<body>
    <?php $activePage = 'pray'; include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-md-8">
                <h1 class="prayer-wall-title">
                    <i class="fas fa-hands-praying"></i> Prayer Wall
                </h1>
                <p class="text-muted">Share your prayer requests and intercede for one another</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button id="newPrayerBtn" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> New Prayer
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control border-0" 
                        id="searchInput" 
                        placeholder="Search prayer requests..."
                        aria-label="Search prayers"
                    >
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="prayer-tabs-container mb-4">
            <ul class="nav nav-pills prayer-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active prayer-tab" id="tab-all" data-category="" role="tab">
                        <i class="fas fa-list"></i> All
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link prayer-tab" id="tab-lauda" data-category="lauda" role="tab">
                        <i class="fas fa-music"></i> Lauda (Praise)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link prayer-tab" id="tab-multumire" data-category="multumire" role="tab">
                        <i class="fas fa-hands-praying"></i> Mulțumire (Thanksgiving)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link prayer-tab" id="tab-cerere" data-category="cerere" role="tab">
                        <i class="fas fa-heart"></i> Cerere (Request)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link prayer-tab" id="tab-mijlocire" data-category="mijlocire" role="tab">
                        <i class="fas fa-dove"></i> Mijlocire (Intercession)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link prayer-tab" id="tab-marturisire" data-category="marturisire" role="tab">
                        <i class="fas fa-cross"></i> Mărturisire (Confession)
                    </button>
                </li>
            </ul>
        </div>

        <!-- Prayer Cards Container -->
        <div id="prayersContainer" class="row">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- New Prayer Modal -->
    <div id="newPrayerModal" class="prayer-modal">
        <div class="prayer-modal-content">
            <div class="prayer-modal-header">
                <h5 class="modal-title">Share a Prayer Request</h5>
                <button type="button" class="btn-close" id="closeNewPrayerModal"></button>
            </div>
            <form id="newPrayerForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="prayerTitle" class="form-label">Title *</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="prayerTitle" 
                            placeholder="What would you like prayer for?"
                            maxlength="200"
                            required
                        >
                        <small class="form-text text-muted" id="titleCount">0/200 characters</small>
                    </div>

                    <div class="mb-3">
                        <label for="prayerDescription" class="form-label">Details (Optional)</label>
                        <textarea 
                            class="form-control" 
                            id="prayerDescription" 
                            placeholder="Share more details about your request..."
                            rows="4"
                            maxlength="1000"
                        ></textarea>
                        <small class="form-text text-muted" id="descCount">0/1000 characters</small>
                    </div>

                    <div class="mb-3">
                        <label for="prayerCategory" class="form-label">Category *</label>
                        <select class="form-select" id="prayerCategory" required>
                            <option value="">Select a category...</option>
                            <option value="lauda">Lauda (Praise)</option>
                            <option value="multumire">Mulțumire (Thanksgiving)</option>
                            <option value="cerere">Cerere (Request)</option>
                            <option value="mijlocire">Mijlocire (Intercession)</option>
                            <option value="marturisire">Mărturisire (Confession)</option>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="prayerAnonymous"
                        >
                        <label class="form-check-label" for="prayerAnonymous">
                            Post anonymously
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelNewPrayer">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitNewPrayer">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="submitSpinner"></span>
                        Share Prayer
                    </button>
                </div>
            </form>
            <div id="newPrayerMessage" class="alert alert-dismissible d-none mt-3" role="alert">
                <span id="newPrayerMessageText"></span>
                <button type="button" class="btn-close" id="closeNewPrayerMessage"></button>
            </div>
        </div>
    </div>

    <!-- Prayer Details Modal -->
    <div id="prayerDetailsModal" class="prayer-modal">
        <div class="prayer-modal-content prayer-details-modal">
            <div class="prayer-modal-header">
                <h5 class="modal-title" id="detailsTitle">Prayer Details</h5>
                <button type="button" class="btn-close" id="closePrayerDetailsModal"></button>
            </div>
            <div class="modal-body">
                <div class="prayer-details-content">
                    <h4 id="detailsRequestTitle" class="mb-3"></h4>
                    
                    <div class="prayer-meta mb-3 pb-3 border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Submitter</small>
                                <strong id="detailsSubmitter"></strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Category</small>
                                <span id="detailsCategory" class="badge"></span>
                            </div>
                        </div>
                    </div>

                    <div id="detailsDescription" class="mb-4"></div>

                    <!-- Emoticon Reaction Section -->
                    <div class="prayer-reactions mb-4">
                        <div class="mb-3">
                            <label class="form-label"><strong>Add Reaction</strong></label>
                            <div class="emoticon-picker" id="emoticonPicker">
                                <button type="button" class="emoticon-btn" data-emoticon="🙏">🙏</button>
                                <button type="button" class="emoticon-btn" data-emoticon="❤️">❤️</button>
                                <button type="button" class="emoticon-btn" data-emoticon="😢">😢</button>
                                <button type="button" class="emoticon-btn" data-emoticon="😊">😊</button>
                                <button type="button" class="emoticon-btn" data-emoticon="🙌">🙌</button>
                                <button type="button" class="emoticon-btn" data-emoticon="✝️">✝️</button>
                            </div>
                        </div>
                        <div id="emoticonsDisplay" class="mb-3"></div>
                    </div>

                    <!-- Praying Button Section -->
                    <div class="prayer-praying mb-4">
                        <button 
                            type="button" 
                            id="prayingForYouBtn" 
                            class="btn btn-outline-primary"
                        >
                            <i class="fas fa-hands-praying"></i> Praying for You
                        </button>
                        <div id="prayingCount" class="mt-2">
                            <small class="text-muted">
                                <strong id="prayingCountNum">0</strong> people are praying
                                <button type="button" class="btn btn-link btn-sm p-0 ms-2" id="seePrayingBtn">See who</button>
                            </small>
                        </div>
                        <div id="prayingList" class="mt-2 d-none">
                            <small class="text-muted d-block mb-2">People praying for this request:</small>
                            <div id="prayingUsersList" class="list-group list-group-flush"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="detailsFooter">
                <!-- Edit/Delete buttons will be added here if user is owner/admin -->
            </div>

            <div id="detailsMessage" class="alert alert-dismissible d-none mt-3" role="alert">
                <span id="detailsMessageText"></span>
                <button type="button" class="btn-close" id="closeDetailsMessage"></button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="pray_script.js"></script>
</body>
</html>
