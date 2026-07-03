<?php
session_start();

// 1. Dapatkan nama group daripada URL parameter atau nama folder semasa
if (!isset($_GET['group'])) {
    $group = basename(dirname(__FILE__));
} else {
    $group = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['group']);
}

// 2. Panggil db.php
$db_paths = [
    'db.php',
    '../db.php',
    '../../db.php',
    '../../All/db.php',
    '../All/db.php',
];

$db_found = false;
$conn = null;

foreach ($db_paths as $path) {
    if (file_exists($path)) {
        include $path;
        $db_found = true;
        break;
    }
}

// If db.php not found or connection failed, try direct connection
if (!$db_found || !isset($conn) || $conn->connect_error) {
    try {
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'gr08';
        
        $conn = new mysqli($host, $username, $password, $database);
        
        if ($conn->connect_error) {
            $conn = new mysqli($host, $username, $password);
            if (!$conn->connect_error) {
                $conn->query("CREATE DATABASE IF NOT EXISTS gr08");
                $conn->select_db('gr08');
            }
        }
        $conn->set_charset("utf8mb4");
    } catch (Exception $e) {
        $conn = null;
    }
}

// ===== HANDLE DESCRIPTION UPDATE =====
$updateMessage = '';
$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_description') {
    $matric_no = trim($_POST['matric_no'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (!empty($matric_no) && !empty($description)) {
        // Check if student exists and add description column if not exists
        $conn->query("ALTER TABLE student ADD COLUMN IF NOT EXISTS outfit_description TEXT");
        
        $sql = "UPDATE student SET outfit_description = ? WHERE matric_no = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $description, $matric_no);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $updateMessage = "✅ Description updated successfully for " . htmlspecialchars($matric_no);
                    $updateSuccess = true;
                } else {
                    $updateMessage = "⚠️ No changes made. Please check the matric number.";
                }
            } else {
                $updateMessage = "❌ Error updating description: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $updateMessage = "⚠️ Please enter both Matric Number and Description.";
    }
}

// 3. Get ALL students from database
$students = [];
if ($conn && !$conn->connect_error) {
    // Check if outfit_description column exists, if not add it
    $checkColumn = $conn->query("SHOW COLUMNS FROM student LIKE 'outfit_description'");
    if ($checkColumn->num_rows == 0) {
        $conn->query("ALTER TABLE student ADD COLUMN outfit_description TEXT");
    }
    
    $sql = "SELECT matric_no, full_name, group_no, photoStu, photoStu_date, outfit_description 
            FROM student 
            ORDER BY full_name ASC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        $stmt->close();
    }
    $conn->close();
}

// If no students found in gr08.student, try to get from mmdb2026.vstu
if (empty($students)) {
    try {
        $conn_mmdb = new mysqli('localhost', 'root', '', 'mmdb2026');
        if (!$conn_mmdb->connect_error) {
            $sql_vstu = "SELECT matric_no, full_name, group_no, photoStu, photoStu_date 
                        FROM vstu 
                        ORDER BY full_name ASC";
            
            if ($stmt = $conn_mmdb->prepare($sql_vstu)) {
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $row['outfit_description'] = null;
                    $students[] = $row;
                }
                $stmt->close();
            }
            $conn_mmdb->close();
        }
    } catch (Exception $e) {
        // Fallback - continue with empty students
    }
}

// Count students with descriptions
$withDescription = 0;
foreach ($students as $s) {
    if (!empty($s['outfit_description'])) $withDescription++;
}

// ===== BASE URL =====
$base_url = "https://bitp3353.utem.edu.my/2026/all/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleScope - Student Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-main: #060b13;
            --bg-sidebar: #0a111e;
            --bg-card: #0e1726;
            --accent-blue: #007aff;
            --text-light: #ffffff;
            --text-muted: #8892b0;
            --border-color: #1e293b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-light);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 260px;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 20px;
            flex-shrink: 0;
        }

        .logo-area {
            margin-bottom: 40px;
        }

        .logo-area h1 {
            font-size: 22px;
            color: var(--text-light);
            letter-spacing: 1px;
        }

        .logo-area p {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .nav-item {
            text-decoration: none;
            padding: 14px 16px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            color: var(--text-muted);
        }

        .nav-item i {
            font-size: 18px;
        }

        .nav-item:hover {
            background-color: rgba(0, 122, 255, 0.08);
            color: var(--text-light);
        }

        .nav-item.active {
            background-color: rgba(0, 122, 255, 0.15);
            border-color: var(--accent-blue);
            color: var(--text-light);
            font-weight: 500;
        }

        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            padding: 30px;
            background: var(--bg-main);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
            flex-wrap: wrap;
        }

        .course-tag {
            background: rgba(0, 122, 255, 0.2);
            color: #4cc3ff;
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 13px;
        }

        .overview-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-blue);
            font-size: 20px;
        }

        .stat-info h3 {
            font-size: 20px;
            margin-bottom: 2px;
        }

        .stat-info p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .search-bar-container {
            position: relative;
            margin-bottom: 20px;
        }

        .search-input {
            width: 100%;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 14px 14px 14px 45px;
            color: var(--text-light);
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }

        .search-input:focus {
            border-color: var(--accent-blue);
        }

        .search-bar-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .filter-tags {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            align-items: center;
        }

        .tag-btn {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .tag-btn:hover {
            border-color: var(--accent-blue);
            color: var(--text-light);
            background-color: rgba(0, 122, 255, 0.08);
        }

        .tag-btn.active {
            border-color: var(--accent-blue);
            color: var(--text-light);
            background-color: rgba(0, 122, 255, 0.15);
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .item-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
        }

        .item-card:hover {
            transform: translateY(-4px);
            border-color: #2e3d56;
        }

        .image-placeholder {
            height: 220px;
            background-color: #111b2b;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .student-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-photo-icon {
            font-size: 48px;
            color: var(--text-muted);
        }

        .card-details {
            padding: 18px 20px 20px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title .badge {
            font-size: 10px;
            background: rgba(0, 122, 255, 0.2);
            color: #4cc3ff;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        .metadata-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .meta-label {
            color: var(--text-muted);
        }

        .meta-value {
            color: #b0c4f0;
            font-weight: 500;
        }

        .card-date {
            border-top: 1px solid var(--border-color);
            padding-top: 10px;
            margin-top: 12px;
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .empty-state {
            color: var(--text-muted);
            grid-column: 1/-1;
            text-align: center;
            padding: 40px 0;
        }

        /* Description Update Form */
        .update-form-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .update-form-container h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: var(--text-light);
        }

        .update-form-container p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 15px;
        }

        .form-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group {
            flex: 1;
            min-width: 180px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px 12px;
            color: var(--text-light);
            font-size: 13px;
            outline: none;
            transition: 0.2s;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--accent-blue);
        }

        .form-group textarea {
            min-height: 60px;
            resize: vertical;
        }

        .btn-update {
            background: var(--accent-blue);
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
            min-width: 120px;
            height: 42px;
        }

        .btn-update:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }

        .update-message {
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 13px;
        }

        .update-message.success {
            background: rgba(0, 200, 0, 0.15);
            border: 1px solid rgba(0, 200, 0, 0.3);
            color: #4cc3ff;
        }

        .update-message.error {
            background: rgba(255, 0, 0, 0.15);
            border: 1px solid rgba(255, 0, 0, 0.3);
            color: #ff6b6b;
        }

        .update-message.warning {
            background: rgba(255, 200, 0, 0.15);
            border: 1px solid rgba(255, 200, 0, 0.3);
            color: #ffd93d;
        }

        .description-text {
            font-size: 12px;
            color: var(--text-muted);
            font-style: italic;
            margin-top: 4px;
            padding: 6px 10px;
            background: rgba(255,255,255,0.03);
            border-radius: 4px;
            border-left: 2px solid var(--accent-blue);
        }

        .description-text.has-desc {
            color: var(--text-light);
            border-left-color: #4cc3ff;
        }

        .desc-badge {
            display: inline-block;
            background: rgba(0, 122, 255, 0.15);
            color: #4cc3ff;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
                overflow-y: auto;
            }
            .sidebar {
                width: 100%;
                height: auto;
            }
            .form-row {
                flex-direction: column;
            }
            .form-group {
                min-width: 100%;
            }
            .btn-update {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-area">
            <h1>STYLESCOPE</h1>
            <p>Intelligent Fashion Metadata Retrieval</p>
        </div>
        <div class="nav-menu">
            <!-- HOME BUTTON ADDED HERE -->
            <a href="https://bitp3353.utem.edu.my/2026/all/GroupMDB/GR08/index.php?group=GR08" class="nav-item" target="_blank">
                <i class="fa-solid fa-house"></i>
                <div>
                    <p style="color:var(--text-light); font-weight:600;">HOME</p>
                    <p style="font-size:11px;">Return to main page</p>
                </div>
            </a>
            <a href="gallery.php" class="nav-item active">
                <i class="fa-solid fa-images"></i>
                <div>
                    <p style="color:var(--text-light); font-weight:600;">STUDENT GALLERY</p>
                    <p style="font-size:11px;">Explore outfit descriptions</p>
                </div>
            </a>
            <a href="search.php" class="nav-item">
                <i class="fa-solid fa-magnifying-glass"></i>
                <div>
                    <p style="color:var(--text-light); font-weight:600;">SMART SEARCH</p>
                    <p style="font-size:11px;">ABR, TBR & CBR retrieval</p>
                </div>
            </a>
            <a href="analytic.php" class="nav-item">
                <i class="fa-solid fa-chart-simple"></i>
                <div>
                    <p style="color:var(--text-light); font-weight:600;">TREND ANALYTICS</p>
                    <p style="font-size:11px;">Colors, styles & insights</p>
                </div>
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="header-top">
            <div>
                <h2>Student Gallery</h2>
                <p style="color: var(--text-muted); font-size:13px; margin-top:4px;">Browse students and their outfit descriptions.</p>
            </div>
            <div class="course-tag">BITP 3353</div>
        </div>

        <!-- Description Update Form -->
        <div class="update-form-container">
            <h3><i class="fa-solid fa-pen-to-square" style="color: var(--accent-blue); margin-right: 8px;"></i> Update Outfit Description</h3>
            <p>Enter your matric number and describe your outfit color/style. This will be displayed on your profile card.</p>
            
            <form method="POST" action="" onsubmit="return validateForm()">
                <input type="hidden" name="action" value="update_description">
                <div class="form-row">
                    <div class="form-group">
                        <label for="matric_no"><i class="fa-regular fa-id-card"></i> Matric Number</label>
                        <input type="text" id="matric_no" name="matric_no" placeholder="e.g., B032310004" required>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label for="description"><i class="fa-regular fa-comment"></i> Outfit Description</label>
                        <textarea id="description" name="description" placeholder="e.g., Black t-shirt with blue jeans, white sneakers" required></textarea>
                    </div>
                    <div class="form-group" style="flex: 0 0 auto;">
                        <button type="submit" class="btn-update"><i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> Update</button>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($updateMessage)): ?>
                <div class="update-message <?php echo $updateSuccess ? 'success' : (strpos($updateMessage, '⚠️') !== false ? 'warning' : 'error'); ?>">
                    <?php echo $updateMessage; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="overview-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-user-group"></i></div>
                <div class="stat-info">
                    <h3 id="totalCount"><?php echo count($students); ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                <div class="stat-info">
                    <h3 id="withDescription"><?php echo $withDescription; ?></h3>
                    <p>With Descriptions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <h3><?php echo count(array_unique(array_column($students, 'group_no'))); ?></h3>
                    <p>Groups</p>
                </div>
            </div>
        </div>

        <div class="search-bar-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="gallerySearchInput" class="search-input" placeholder="Search by name, matric, or description..." onkeyup="filterGallery()">
        </div>

        <div class="filter-tags">
            <span style="font-size: 13px; color: var(--text-muted); margin-right: 5px;">Quick Filters:</span>
            <button class="tag-btn active" data-filter="all" onclick="filterByTag('all', this)">All</button>
            <button class="tag-btn" data-filter="hasdesc" onclick="filterByTag('hasdesc', this)">Has Description</button>
            <button class="tag-btn" data-filter="nodesc" onclick="filterByTag('nodesc', this)">No Description</button>
        </div>

        <div class="student-grid" id="studentGrid">
            <!-- Cards will be populated by JavaScript -->
        </div>
    </div>

    <script>
        // ---------- STUDENT DATA FROM DATABASE ----------
        const baseUrl = 'https://bitp3353.utem.edu.my/2026/all/';
        
        const studentData = <?php 
            $items = [];
            foreach ($students as $student) {
                $photoPath = !empty($student['photoStu']) ? $student['photoStu'] : null;
                
                if ($photoPath && strpos($photoPath, '/') === 0) {
                    $photoPath = ltrim($photoPath, '/');
                }
                
                if ($photoPath && strpos($photoPath, 'uploads/') !== 0) {
                    $photoPath = 'uploads/' . $photoPath;
                }
                
                $items[] = [
                    'matric_no' => $student['matric_no'] ?? '-',
                    'full_name' => $student['full_name'] ?? 'Unknown',
                    'photo' => $photoPath,
                    'photo_date' => $student['photoStu_date'] ?? null,
                    'group_no' => $student['group_no'] ?? '-',
                    'outfit_description' => $student['outfit_description'] ?? null
                ];
            }
            echo json_encode($items);
        ?>;

        // ---------- RENDER GALLERY ----------
        let currentFilter = 'all';
        let currentSearch = '';

        function renderGallery() {
            const grid = document.getElementById('studentGrid');
            grid.innerHTML = '';

            if (studentData.length === 0) {
                grid.innerHTML = '<div class="empty-state">No students found.</div>';
                updateStats([]);
                return;
            }

            const filtered = studentData.filter(item => {
                const searchMatch = !currentSearch || 
                    item.full_name.toLowerCase().includes(currentSearch) ||
                    item.matric_no.toLowerCase().includes(currentSearch) ||
                    item.group_no.toLowerCase().includes(currentSearch) ||
                    (item.outfit_description && item.outfit_description.toLowerCase().includes(currentSearch));
                
                let tagMatch = true;
                if (currentFilter === 'hasdesc') {
                    tagMatch = item.outfit_description !== null && item.outfit_description !== '';
                } else if (currentFilter === 'nodesc') {
                    tagMatch = item.outfit_description === null || item.outfit_description === '';
                }
                
                return searchMatch && tagMatch;
            });

            if (filtered.length === 0) {
                grid.innerHTML = '<div class="empty-state">No matching students found.</div>';
            } else {
                filtered.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'item-card';
                    card.setAttribute('data-name', item.full_name);
                    card.setAttribute('data-matric', item.matric_no);
                    
                    let photoDateDisplay = 'No photo uploaded';
                    let photoIcon = 'fa-regular fa-circle-xmark';
                    if (item.photo_date && item.photo_date !== '0000-00-00') {
                        const dateObj = new Date(item.photo_date);
                        if (!isNaN(dateObj.getTime())) {
                            const options = { year: 'numeric', month: 'short', day: 'numeric' };
                            photoDateDisplay = 'Photo uploaded: ' + dateObj.toLocaleDateString('en-US', options);
                            photoIcon = 'fa-regular fa-calendar';
                        } else {
                            photoDateDisplay = 'Photo uploaded: ' + item.photo_date;
                            photoIcon = 'fa-regular fa-calendar';
                        }
                    }
                    
                    let imageUrl = '';
                    if (item.photo) {
                        imageUrl = baseUrl + item.photo;
                    }
                    
                    // Description HTML with badge
                    let descriptionHtml = '';
                    if (item.outfit_description) {
                        descriptionHtml = `
                            <div class="description-text has-desc">
                                <i class="fa-regular fa-comment" style="margin-right: 4px;"></i>
                                ${item.outfit_description}
                                <span class="desc-badge" style="margin-left: 6px;">✓</span>
                            </div>
                        `;
                    } else {
                        descriptionHtml = `
                            <div class="description-text">
                                <i class="fa-regular fa-circle" style="margin-right: 4px;"></i>
                                No outfit description yet
                            </div>
                        `;
                    }
                    
                    card.innerHTML = `
                        <div class="image-placeholder">
                            ${imageUrl ? 
                                `<img src="${imageUrl}" alt="${item.full_name}" class="student-photo" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\\'fa-solid fa-user no-photo-icon\\'></i>'">` :
                                `<i class="fa-solid fa-user no-photo-icon"></i>`
                            }
                        </div>
                        <div class="card-details">
                            <div class="card-title">
                                ${item.full_name}
                                <span class="badge">${item.group_no}</span>
                            </div>
                            <div class="metadata-row"><span class="meta-label">Matric No:</span><span class="meta-value">${item.matric_no}</span></div>
                            <div class="metadata-row"><span class="meta-label">Group:</span><span class="meta-value">${item.group_no}</span></div>
                            ${descriptionHtml}
                            <div class="card-date"><i class="${photoIcon}"></i> ${photoDateDisplay}</div>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            }

            updateStats(filtered);
        }

        function updateStats(items) {
            const total = items.length;
            document.getElementById('totalCount').textContent = total;

            const withDesc = items.filter(item => item.outfit_description !== null && item.outfit_description !== '').length;
            document.getElementById('withDescription').textContent = withDesc;
        }

        function filterGallery() {
            const input = document.getElementById('gallerySearchInput');
            currentSearch = input.value.toLowerCase().trim();
            renderGallery();
        }

        function filterByTag(tag, element) {
            document.querySelectorAll('.filter-tags .tag-btn').forEach(b => b.classList.remove('active'));
            element.classList.add('active');
            currentFilter = tag;
            renderGallery();
        }

        // ---------- FORM VALIDATION ----------
        function validateForm() {
            const matric = document.getElementById('matric_no').value.trim();
            const desc = document.getElementById('description').value.trim();
            
            if (!matric) {
                alert('Please enter your Matric Number.');
                document.getElementById('matric_no').focus();
                return false;
            }
            if (!desc) {
                alert('Please enter your outfit description.');
                document.getElementById('description').focus();
                return false;
            }
            return true;
        }

        // ---------- INITIALIZE ----------
        window.onload = function() {
            renderGallery();
        };
    </script>
</body>
</html>