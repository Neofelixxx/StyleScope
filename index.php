<?php
session_start();

// 1. Dapatkan nama group daripada URL parameter atau nama folder semasa
if (!isset($_GET['group'])) {
    $group = basename(dirname(__FILE__));
} else {
    $group = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['group']);
}

// 2. Panggil db.php - Try multiple paths
$db_paths = [
    'db.php',           // Same folder
    '../db.php',        // Parent folder
    '../../db.php',     // Two levels up
    '../../All/db.php', // All folder
    '../All/db.php',    // Parent/All
];

$db_found = false;
foreach ($db_paths as $path) {
    if (file_exists($path)) {
        include $path;
        $db_found = true;
        break;
    }
}

if (!$db_found) {
    // Fallback: define connection directly if db.php not found
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'gr08';
    
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
}

// 3. Ambil data ahli kumpulan dari table student
$members = [];
$sql = "SELECT S.matric_no, S.full_name, S.group_no, S.photoStu, S.photoStu_date 
        FROM student S 
        WHERE S.group_no = ?
        ORDER BY S.full_name ASC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $group);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
    $stmt->close();
}
$conn->close(); 
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleScope - Ahli Kumpulan <?php echo htmlspecialchars($group); ?></title>
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
            background: var(--bg-main);
            color: var(--text-light);
            min-height: 100vh;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .header h1 span {
            color: var(--accent-blue);
        }

        .header p {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .group-badge {
            background: rgba(0, 122, 255, 0.15);
            border: 1px solid var(--accent-blue);
            padding: 8px 24px;
            border-radius: 8px;
            font-size: 1.4rem;
            font-weight: bold;
            color: #4cc3ff;
        }

        .nav-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .nav-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 18px 20px;
            text-decoration: none;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.3s ease;
        }

        .nav-card:hover {
            border-color: var(--accent-blue);
            transform: translateY(-2px);
            background: rgba(0, 122, 255, 0.05);
        }

        .nav-card i {
            font-size: 24px;
            color: var(--accent-blue);
            width: 40px;
            text-align: center;
        }

        .nav-card .nav-info h4 {
            font-size: 14px;
            font-weight: 600;
        }

        .nav-card .nav-info p {
            font-size: 11px;
            color: var(--text-muted);
        }

        .table-container {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255,255,255,0.02);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-color);
            font-size: 1rem;
        }

        th {
            background: var(--bg-card);
            color: #4cc3ff;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: rgba(255,255,255,0.02);
        }

        .matrix-code {
            color: #4cc3ff;
            font-weight: bold;
            font-family: monospace;
            font-size: 1rem;
        }

        .bil-col {
            font-weight: bold;
            color: var(--text-muted);
        }

        .student-photo {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
            background: var(--bg-card);
        }

        .photo-placeholder {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 18px;
        }

        .empty-state {
            text-align: center;
            color: #ff6b6b;
            padding: 40px;
            font-size: 1.1rem;
        }

        .btn-action {
            display: inline-block;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--accent-blue);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.85;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #2a3a55;
            color: white;
        }

        .btn-secondary:hover {
            background: #3a4a6a;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            opacity: 0.85;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ffc107;
            color: #1a1a1a;
        }

        .btn-warning:hover {
            opacity: 0.85;
            transform: translateY(-2px);
        }

        .btn-gallery {
            background: linear-gradient(135deg, #007aff, #0056b3);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
        }

        .btn-gallery:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 122, 255, 0.4);
            opacity: 0.95;
        }

        .action-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            body { padding: 15px; }
            .header { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; justify-content: space-between; }
            th, td { padding: 12px 15px; font-size: 0.9rem; }
            .group-badge { font-size: 1.1rem; padding: 6px 16px; }
            .nav-cards { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>👥 <span>STYLESCOPE</span> · Ahli Kumpulan</h1>
        <p>Senarai ahli kumpulan dan akses ke sistem Fashion Metadata</p>
    </div>
    <div class="header-actions">
        <div class="group-badge">
            GROUP: <?php echo htmlspecialchars($group); ?>
        </div>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">BIL</th>
                <th style="width: 70px;">GAMBAR</th>
                <th>NAMA PENUH</th>
                <th style="width: 250px;">NO. MATRIK</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($members)): ?>
                <tr>
                    <td colspan="4" class="empty-state">
                        <i class="fa-solid fa-user-slash" style="margin-right: 10px;"></i>
                        Tiada ahli kumpulan ditemui untuk group "<?php echo htmlspecialchars($group); ?>".
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($members as $index => $row): ?>
                    <tr>
                        <td class="bil-col"><?php echo $index + 1; ?></td>
                        <td>
                            <?php 
                            // Build image path - use /2026/all/ base URL
                            $baseUrl = '/2026/all/';
                            $photoPath = !empty($row['photoStu']) ? $row['photoStu'] : null;
                            $imgSrc = '';
                            if ($photoPath) {
                                // Remove any leading slash
                                if (strpos($photoPath, '/') === 0) {
                                    $photoPath = ltrim($photoPath, '/');
                                }
                                $imgSrc = $baseUrl . $photoPath;
                            }
                            ?>
                            <?php if (!empty($row['photoStu'])): ?>
                                <img src="<?php echo $imgSrc; ?>" 
                                     alt="<?php echo htmlspecialchars($row['full_name']); ?>" 
                                     class="student-photo"
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'photo-placeholder\'><i class=\'fa-solid fa-user\'></i></div>'">
                            <?php else: ?>
                                <div class="photo-placeholder">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="text-transform: uppercase; font-weight: 500;">
                            <?php echo htmlspecialchars($row['full_name'] ?? '-'); ?>
                        </td>
                        <td class="matrix-code"><?php echo htmlspecialchars($row['matric_no'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ACTION BAR -->
<div class="action-bar">
    <a href="../../dashboard.php?group=<?php echo urlencode($group); ?>" class="btn-action btn-secondary">
        <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> Dashboard
    </a>
    
    <a href="gallery.php?group=<?php echo urlencode($group); ?>" class="btn-action btn-gallery">
        <i class="fa-solid fa-right-to-bracket" style="margin-right: 8px;"></i> Go To System Interface
    </a>

</div>

</body>
</html>