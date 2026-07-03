<?php
session_start();

// 1. Panggil db.php
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

// Check if outfit_description column exists, if not add it
if ($conn && !$conn->connect_error) {
    $checkColumn = $conn->query("SHOW COLUMNS FROM student LIKE 'outfit_description'");
    if ($checkColumn->num_rows == 0) {
        $conn->query("ALTER TABLE student ADD COLUMN outfit_description TEXT");
    }
}

// Get student data with outfit descriptions
$students = [];
if ($conn && !$conn->connect_error) {
    $sql = "SELECT matric_no, full_name, group_no, photoStu, photoStu_date, outfit_description 
            FROM student 
            ORDER BY group_no, full_name ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
}

// Get CBR statistics
$cbrStats = [];
if ($conn && !$conn->connect_error) {
    $sql = "SELECT formality_result, COUNT(*) as count FROM cbr_analysis GROUP BY formality_result";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cbrStats[$row['formality_result']] = $row['count'];
        }
    }
}

// Get search statistics
$searchStats = [];
if ($conn && !$conn->connect_error) {
    $sql = "SELECT search_type, COUNT(*) as count FROM search_log GROUP BY search_type";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $searchStats[$row['search_type']] = $row['count'];
        }
    }
    $conn->close();
}

// ===== Extract colors from outfit_description =====
$colorKeywords = [
    'black' => '#1a1a1a',
    'white' => '#ffffff',
    'blue' => '#007aff',
    'red' => '#ff3b30',
    'green' => '#34c759',
    'yellow' => '#ffcc00',
    'orange' => '#ff9500',
    'purple' => '#af52de',
    'pink' => '#ff2d55',
    'brown' => '#8d6e63',
    'grey' => '#8e8e93',
    'gray' => '#8e8e93',
    'navy' => '#1a2a4a',
    'beige' => '#d4b896',
    'cream' => '#f5f0e1',
    'gold' => '#ffd700',
    'silver' => '#c0c0c0',
    'maroon' => '#800000',
    'teal' => '#008080',
    'coral' => '#ff7f50',
    'lavender' => '#e6e6fa',
    'peach' => '#ffdab9',
    'mint' => '#98ff98',
    'olive' => '#808000',
    'denim' => '#1565c0',
    'khaki' => '#c3b091',
    'taupe' => '#483c32',
    'burgundy' => '#800020',
    'mustard' => '#e1ad01',
    'ruby' => '#e0115f',
    'emerald' => '#50c878',
    'sapphire' => '#0f52ba'
];

$colorCount = [];
$hasDescription = 0;

foreach ($students as $s) {
    if (!empty($s['outfit_description'])) {
        $hasDescription++;
        $desc = strtolower($s['outfit_description']);
        $found = false;
        
        foreach ($colorKeywords as $color => $hex) {
            if (strpos($desc, $color) !== false) {
                $colorCount[$color] = ($colorCount[$color] ?? 0) + 1;
                $found = true;
                break;
            }
        }
        // If no color keyword found, group as "Other"
        if (!$found) {
            $colorCount['Other'] = ($colorCount['Other'] ?? 0) + 1;
        }
    }
}

// Sort colors by count (highest first)
arsort($colorCount);
$topColors = array_slice($colorCount, 0, 6);

// Calculate statistics
$totalStudents = count($students);
$withPhoto = 0;
foreach ($students as $s) {
    if (!empty($s['photoStu'])) $withPhoto++;
}
$withoutPhoto = $totalStudents - $withPhoto;

$groupCount = [];
foreach ($students as $s) {
    $group = !empty($s['group_no']) ? $s['group_no'] : 'Unknown';
    $groupCount[$group] = ($groupCount[$group] ?? 0) + 1;
}
arsort($groupCount);
$topGroups = array_slice($groupCount, 0, 5);

// Get top months for photo uploads
$monthCount = [];
foreach ($students as $s) {
    if (!empty($s['photoStu_date']) && $s['photoStu_date'] !== '0000-00-00') {
        $month = date('M Y', strtotime($s['photoStu_date']));
        $monthCount[$month] = ($monthCount[$month] ?? 0) + 1;
    }
}
arsort($monthCount);
$topMonths = array_slice($monthCount, 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleScope - Trend Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-light); display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; padding: 20px; flex-shrink: 0; }
        .logo-area { margin-bottom: 40px; }
        .logo-area h1 { font-size: 22px; color: var(--text-light); letter-spacing: 1px; }
        .logo-area p { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .nav-item { text-decoration: none; padding: 14px 16px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: all 0.2s ease; border: 1px solid transparent; color: var(--text-muted); }
        .nav-item i { font-size: 18px; }
        .nav-item:hover { background-color: rgba(0, 122, 255, 0.08); color: var(--text-light); }
        .nav-item.active { background-color: rgba(0, 122, 255, 0.15); border-color: var(--accent-blue); color: var(--text-light); font-weight: 500; }
        .main-container { flex: 1; display: flex; flex-direction: column; overflow-y: auto; padding: 30px; background: var(--bg-main); }
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; flex-wrap: wrap; }
        .course-tag { background: rgba(0, 122, 255, 0.2); color: #4cc3ff; padding: 6px 14px; border-radius: 4px; font-weight: 600; font-size: 13px; }
        .analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
        .chart-box { 
            background-color: var(--bg-card); 
            border: 1px solid var(--border-color); 
            border-radius: 10px; 
            padding: 20px; 
            height: 300px; 
            position: relative; 
            display: flex;
            flex-direction: column;
        }
        .chart-box h4 { 
            margin-bottom: 15px; 
            font-size: 14px; 
            color: var(--text-muted); 
            font-weight: 500;
            flex-shrink: 0;
        }
        .chart-box .chart-wrapper {
            flex: 1;
            position: relative;
            min-height: 0;
            width: 100%;
        }
        .chart-box .chart-wrapper canvas {
            width: 100% !important;
            height: 100% !important;
            max-height: 100%;
        }
        .insight-box { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 25px; }
        .insight-box h4 { margin-bottom: 15px; font-size: 14px; color: var(--text-muted); font-weight: 500; }
        .insight-list { list-style: none; line-height: 2; }
        .insight-list li { display: flex; align-items: flex-start; gap: 10px; padding: 6px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.03); }
        .insight-list li:last-child { border-bottom: none; }
        .insight-list i { color: #4cc3ff; margin-top: 4px; font-size: 14px; }
        .insight-list .highlight { color: #4cc3ff; font-weight: 600; }
        .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-mini { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 15px; text-align: center; }
        .stat-mini .number { font-size: 28px; font-weight: 700; color: var(--accent-blue); }
        .stat-mini .label { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .color-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
            border: 1px solid rgba(255,255,255,0.2);
            vertical-align: middle;
        }
        @media (max-width: 768px) {
            body { flex-direction: column; overflow-y: auto; }
            .sidebar { width: 100%; height: auto; }
            .analytics-grid { grid-template-columns: 1fr; }
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
            <a href="gallery.php" class="nav-item">
                <i class="fa-solid fa-images"></i>
                <div>
                    <p style="color:var(--text-light); font-weight:600;">STUDENT GALLERY</p>
                    <p style="font-size:11px;">Browse all students</p>
                </div>
            </a>
            <a href="search.php" class="nav-item">
                <i class="fa-solid fa-magnifying-glass"></i>
                <div>
                    <p style="color:var(--text-light); font-weight:600;">SMART SEARCH</p>
                    <p style="font-size:11px;">ABR, TBR & CBR retrieval</p>
                </div>
            </a>
            <a href="analytic.php" class="nav-item active">
                <i class="fa-solid fa-chart-simple"></i>
                <div>
                    <p style="color:var(--text-light); font-weight:600;">TREND ANALYTICS</p>
                    <p style="font-size:11px;">Student insights & statistics</p>
                </div>
            </a>
        </div>
    </div>
    <div class="main-container">
        <div class="header-top">
            <div>
                <h2>Student Analytics Hub</h2>
                <p style="color: var(--text-muted); font-size:13px; margin-top:4px;">Analyze student data, group distribution, and outfit color trends.</p>
            </div>
            <div class="course-tag">BITP 3353</div>
        </div>
        <div class="stat-row">
            <div class="stat-mini"><div class="number"><?php echo $totalStudents; ?></div><div class="label">Total Students</div></div>
            <div class="stat-mini"><div class="number"><?php echo $withPhoto; ?></div><div class="label">With Photos</div></div>
            <div class="stat-mini"><div class="number"><?php echo $withoutPhoto; ?></div><div class="label">Without Photos</div></div>
            <div class="stat-mini"><div class="number"><?php echo $hasDescription; ?></div><div class="label">With Descriptions</div></div>
        </div>
        <div class="analytics-grid">
            <div class="chart-box">
                <h4>Outfit Color Distribution</h4>
                <div class="chart-wrapper">
                    <canvas id="colorPieChart"></canvas>
                </div>
            </div>
            <div class="chart-box">
                <h4>Top 5 Groups Distribution</h4>
                <div class="chart-wrapper">
                    <canvas id="groupBarChart"></canvas>
                </div>
            </div>
        </div>
        <?php if (!empty($cbrStats) || !empty($searchStats)): ?>
        <div class="analytics-grid" style="grid-template-columns: 1fr 1fr; margin-bottom:25px;">
            <?php if (!empty($cbrStats)): ?>
            <div class="chart-box">
                <h4>CBR Analysis Results</h4>
                <div class="chart-wrapper">
                    <canvas id="cbrChart"></canvas>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($searchStats)): ?>
            <div class="chart-box">
                <h4>Search Type Distribution</h4>
                <div class="chart-wrapper">
                    <canvas id="searchChart"></canvas>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="insight-box">
            <h4>System Insights Summary</h4>
            <ul class="insight-list">
                <li><i class="fa-solid fa-circle-check"></i><span>Total students in system: <span class="highlight"><?php echo $totalStudents; ?></span></span></li>
                <li><i class="fa-solid fa-circle-check"></i><span><span class="highlight"><?php echo $totalStudents > 0 ? round(($withPhoto/$totalStudents)*100,1) : 0; ?>%</span> of students have uploaded profile photos.</span></li>
                <li><i class="fa-solid fa-circle-check"></i><span><span class="highlight"><?php echo $hasDescription; ?></span> students have added outfit descriptions.</span></li>
                <li><i class="fa-solid fa-circle-check"></i><span>Number of active groups: <span class="highlight"><?php echo count($groupCount); ?></span></span></li>
                <?php if (!empty($topMonths)): ?>
                <li><i class="fa-solid fa-circle-check"></i><span>Most photo uploads: <span class="highlight"><?php echo array_key_first($topMonths) . ' (' . current($topMonths) . ' photos)'; ?></span></span></li>
                <?php endif; ?>
                <?php if (!empty($cbrStats)): ?>
                <li><i class="fa-solid fa-circle-check"></i><span>CBR analysis: <span class="highlight"><?php echo $cbrStats['Formal'] ?? 0; ?></span> Formal, <span class="highlight"><?php echo $cbrStats['Non-Formal'] ?? 0; ?></span> Non-Formal</span></li>
                <?php endif; ?>
                <?php if (!empty($searchStats)): ?>
                <li><i class="fa-solid fa-circle-check"></i><span>Total searches: <span class="highlight"><?php echo array_sum($searchStats); ?></span> (ABR: <?php echo $searchStats['ABR'] ?? 0; ?>, TBR: <?php echo $searchStats['TBR'] ?? 0; ?>, CBR: <?php echo $searchStats['CBR'] ?? 0; ?>)</span></li>
                <?php endif; ?>
                <?php if (!empty($topColors)): ?>
                <li><i class="fa-solid fa-circle-check"></i><span>Top outfit colors: 
                    <?php 
                    $colorNames = array_keys($topColors);
                    $colorHex = [];
                    foreach ($colorNames as $name) {
                        if (isset($colorKeywords[$name])) {
                            $colorHex[] = '<span class="color-dot" style="background:' . $colorKeywords[$name] . ';"></span>' . ucfirst($name);
                        } else {
                            $colorHex[] = ucfirst($name);
                        }
                    }
                    echo implode(', ', $colorHex);
                    ?>
                </span></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <script>
        // ---------- DATA FROM DATABASE ----------
        const colorData = <?php 
            $data = [];
            foreach ($topColors as $color => $count) {
                $hex = isset($colorKeywords[$color]) ? $colorKeywords[$color] : '#8e8e93';
                $data[] = ['label' => ucfirst($color), 'value' => $count, 'hex' => $hex];
            }
            echo json_encode($data);
        ?>;

        const groupData = <?php 
            $groups = [];
            foreach ($topGroups as $group => $count) {
                $groups[] = ['label' => $group, 'value' => $count];
            }
            echo json_encode($groups);
        ?>;

        const cbrData = <?php 
            $data = [];
            foreach ($cbrStats as $key => $val) {
                $data[] = ['label' => $key, 'value' => $val];
            }
            echo json_encode($data);
        ?>;

        const searchData = <?php 
            $data = [];
            foreach ($searchStats as $key => $val) {
                $data[] = ['label' => $key, 'value' => $val];
            }
            echo json_encode($data);
        ?>;

        const colorKeywords = <?php 
            $colors = [];
            foreach ($colorKeywords as $name => $hex) {
                $colors[$name] = $hex;
            }
            echo json_encode($colors);
        ?>;

        // ---------- CHART RENDERER ----------
        function renderCharts() {
            // ---- Color Distribution Pie Chart ----
            const colorLabels = colorData.map(item => item.label);
            const colorValues = colorData.map(item => item.value);
            const colorHexes = colorData.map(item => item.hex);
            
            const ctxColor = document.getElementById('colorPieChart').getContext('2d');
            
            // Determine if we have data
            if (colorLabels.length === 0 || colorValues.reduce((a, b) => a + b, 0) === 0) {
                new Chart(ctxColor, {
                    type: 'doughnut',
                    data: {
                        labels: ['No Data'],
                        datasets: [{ 
                            data: [1], 
                            backgroundColor: ['#2a3a55'], 
                            borderWidth: 0 
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { color: '#ffffff', padding: 12, font: { size: 12 } }
                            }
                        },
                        cutout: '60%'
                    }
                });
            } else {
                new Chart(ctxColor, {
                    type: 'doughnut',
                    data: {
                        labels: colorLabels,
                        datasets: [{ 
                            data: colorValues, 
                            backgroundColor: colorHexes,
                            borderWidth: 0 
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { 
                                    color: '#ffffff', 
                                    padding: 12, 
                                    font: { size: 12 },
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }

            // ---- Group Bar Chart ----
            const ctxGroup = document.getElementById('groupBarChart').getContext('2d');
            const colors = ['#4cc3ff', '#007aff', '#ff6b6b', '#ffd93d', '#6bcb77'];
            new Chart(ctxGroup, {
                type: 'bar',
                data: {
                    labels: groupData.length > 0 ? groupData.map(i => i.label) : ['No Data'],
                    datasets: [{
                        label: 'Number of Students',
                        data: groupData.length > 0 ? groupData.map(i => i.value) : [0],
                        backgroundColor: colors.slice(0, groupData.length || 1),
                        borderRadius: 6,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#ffffff', font: { size: 11 } } },
                        y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#ffffff', stepSize: 1 } }
                    }
                }
            });

            // ---- CBR Chart ----
            const cbrLabels = cbrData.map(i => i.label);
            const cbrValues = cbrData.map(i => i.value);
            if (cbrLabels.length > 0) {
                const ctxCbr = document.getElementById('cbrChart').getContext('2d');
                new Chart(ctxCbr, {
                    type: 'doughnut',
                    data: {
                        labels: cbrLabels,
                        datasets: [{ data: cbrValues, backgroundColor: ['#4cc3ff', '#f0a0a0'], borderWidth: 0 }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { color: '#ffffff', padding: 12, font: { size: 12 } }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }

            // ---- Search Chart ----
            const searchLabels = searchData.map(i => i.label);
            const searchValues = searchData.map(i => i.value);
            if (searchLabels.length > 0) {
                const ctxSearch = document.getElementById('searchChart').getContext('2d');
                new Chart(ctxSearch, {
                    type: 'bar',
                    data: {
                        labels: searchLabels,
                        datasets: [{
                            label: 'Searches',
                            data: searchValues,
                            backgroundColor: ['#4cc3ff', '#007aff', '#ff6b6b'],
                            borderRadius: 6,
                            barThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#ffffff', font: { size: 12 } } },
                            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#ffffff', stepSize: 1 } }
                        }
                    }
                });
            }
        }

        window.addEventListener('DOMContentLoaded', () => { renderCharts(); });
    </script>
</body>
</html> 