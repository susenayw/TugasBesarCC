<?php
session_start();

/**
 * Quiz gaje 666 - Main Application
 * Requires: config.php
 */
require_once 'config.php';

// --- ROUTING & LOGIC ---
$view = $_GET['view'] ?? 'landing';
$error = "";

// Logout Logic
if ($view === 'logout') {
    session_destroy();
    header("Location: index.php?view=landing");
    exit();
}

// Signup Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$user'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$user', '$pass')");
        $_SESSION['user'] = $user;
        header("Location: index.php?view=dashboard");
        exit();
    }
}

// Login Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    $u = mysqli_fetch_assoc($res);
    if ($u && password_verify($pass, $u['password'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php?view=dashboard");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}

// Score Submission (via Fetch API)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_quiz') {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }
    $score = intval($_POST['score']);
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $user = $_SESSION['user'];
    $sql = "INSERT INTO leaderboard (username, score, category) VALUES ('$user', '$score', '$cat')";
    mysqli_query($conn, $sql);
    echo json_encode(['status' => 'success']);
    exit();
}

// --- QUESTIONS DATA ---
$questions = [
    'skkm' => [
        ["q" => "Apa kepanjangan dari SKJ yang populer di Indonesia?", "a" => ["Senam Kebugaran Jasmani", "Seni Kesehatan Jiwa", "Senam Kerja Jantung", "Sistem Kebugaran Jaringan"], "correct" => "Senam Kebugaran Jasmani"],
        ["q" => "Tarian Poco-poco berasal dari daerah mana?", "a" => ["Maluku", "Papua", "Sulawesi Utara", "Sumatera Barat"], "correct" => "Maluku"],
        ["q" => "Berapa langkah kaki minimal per hari yang disarankan untuk kesehatan?", "a" => ["2.000", "5.000", "10.000", "50.000"], "correct" => "10.000"],
        ["q" => "Kapan Hari Kesehatan Nasional diperingati di Indonesia?", "a" => ["17 Agustus", "12 November", "28 Oktober", "1 Mei"], "correct" => "12 November"],
        ["q" => "Minuman tradisional jamu yang dikenal untuk pegal linu adalah?", "a" => ["Beras Kencur", "Kunyit Asam", "Cabe Lempuyang", "Pahitan"], "correct" => "Beras Kencur"],
        ["q" => "Istilah 'Isi Piringku' menggantikan konsep lama yaitu?", "a" => ["4 Sehat 5 Sempurna", "Gizi Seimbang", "Piramida Makanan", "Nutrisi Nasional"], "correct" => "4 Sehat 5 Sempurna"],
        ["q" => "Senam Maumere berasal dari provinsi?", "a" => ["NTT", "NTB", "Maluku", "Sulawesi Selatan"], "correct" => "NTT"],
        ["q" => "Aktivitas fisik intensitas sedang sebaiknya dilakukan berapa menit per minggu?", "a" => ["60 menit", "150 menit", "300 menit", "500 menit"], "correct" => "150 menit"],
        ["q" => "Apa nama aplikasi resmi pemerintah Indonesia untuk memantau kesehatan?", "a" => ["PeduliLindungi", "SatuSehat", "HaloDoc", "SehatPedia"], "correct" => "SatuSehat"],
        ["q" => "Nutrisi utama yang dibutuhkan untuk pembentukan otot adalah?", "a" => ["Karbohidrat", "Protein", "Lemak", "Vitamin"], "correct" => "Protein"]
    ],
    'cs' => [
        ["q" => "Arsitektur model AI yang mendasari ChatGPT adalah?", "a" => ["RNN", "CNN", "Transformers", "SVM"], "correct" => "Transformers"],
        ["q" => "Teknologi 'Quantum Supremacy' merujuk pada keunggulan komputer quantum atas?", "a" => ["Komputer Super", "Komputer Klasik", "Sistem Cloud", "AI"], "correct" => "Komputer Klasik"],
        ["q" => "Framework frontend yang paling banyak digunakan di tahun 2025?", "a" => ["React", "Vue", "Angular", "Svelte"], "correct" => "React"],
        ["q" => "Apa itu 'Edge Computing'?", "a" => ["Proses data di pinggiran jaringan", "Proses data di pusat cloud", "Proses data tanpa internet", "Proses data manual"], "correct" => "Proses data di pinggiran jaringan"],
        ["q" => "Bahasa pemrograman utama untuk pengembangan Smart Contracts di Ethereum?", "a" => ["Python", "Solidity", "Rust", "Go"], "correct" => "Solidity"],
        ["q" => "Sistem keamanan 'Zero Trust' berarti?", "a" => ["Percaya semua user", "Tidak percaya siapapun secara default", "Hanya percaya admin", "Tanpa password"], "correct" => "Tidak percaya siapapun secara default"],
        ["q" => "Standard koneksi nirkabel tercepat yang mulai umum di 2025?", "a" => ["WiFi 5", "WiFi 6E", "WiFi 7", "5G"], "correct" => "WiFi 7"],
        ["q" => "Istilah 'LLM' dalam AI adalah singkatan dari?", "a" => ["Large Language Model", "Little Logical Machine", "Low Level Memory", "Long Life Model"], "correct" => "Large Language Model"],
        ["q" => "Platform orkestrasi container yang paling dominan?", "a" => ["Docker", "Kubernetes", "Jenkins", "Terraform"], "correct" => "Kubernetes"],
        ["q" => "Tipe database yang menyimpan data dalam format dokumen JSON?", "a" => ["SQL", "NoSQL (MongoDB)", "Excel", "Flat File"], "correct" => "NoSQL (MongoDB)"]
    ],
    'gajelas' => [
        ["q" => "Menurut teori gajelas, berasal dari gabungan kata apa istilah 'Diare'?", "a" => ["Di & Are", "Dia & Re", "Diar & E", "Dia & Re-lay"], "correct" => "Dia & Re"],
        ["q" => "Apa sekolah terbaik di Amerika yang sukses membuat jutaan orang tetap di jalurnya?", "a" => ["Harvard", "Stanford", "Sekolah Mengemudi", "MIT"], "correct" => "Sekolah Mengemudi"],
        ["q" => "Kenapa kursi dianggap sebagai furnitur yang paling sombong?", "a" => ["Karena harganya mahal", "Karena selalu meminta Anda berlutut sebelum duduk", "Karena tidak mau bergerak", "Karena punya sandaran"], "correct" => "Karena selalu meminta Anda berlutut sebelum duduk"],
        ["q" => "Apa kemampuan manusia sebelum Isaac Newton menemukan gravitasi pada tahun 1666?", "a" => ["Bisa terbang", "Bisa menghilang", "Bisa bernapas di air", "Bisa makan batu"], "correct" => "Bisa terbang"],
        ["q" => "Apa rahasia utama umur panjang kura-kura?", "a" => ["Sering meditasi", "Makan sayur organik", "Tidak lari pagi atau mengejar bus", "Tidur siang 12 jam"], "correct" => "Tidak lari pagi atau mengejar bus"],
        ["q" => "Kenapa smartphone berevolusi menjadi semakin tipis?", "a" => ["Agar lebih ringan", "Agar tidak mudah jatuh ke lubang toilet", "Agar muat di dompet", "Mengikuti selera pasar"], "correct" => "Agar tidak mudah jatuh ke lubang toilet"],
        ["q" => "Apa definisi tidur yang paling akurat menurut ilmu gajelas?", "a" => ["Sesi istirahat total", "Free trial menjadi mati dengan iklan mimpi", "Proses pembersihan otak", "Hiburan gratis malam hari"], "correct" => "Free trial menjadi mati dengan iklan mimpi"],
        ["q" => "Awan sebenarnya adalah kapas bantal para raksasa yang meledak karena...", "a" => ["Kepanasan", "Terlalu banyak memikirkan cicilan rumah", "Kena petir", "Ditiup angin kencang"], "correct" => "Terlalu banyak memikirkan cicilan rumah"],
        ["q" => "Kenapa uang disebut sebagai 'alat tukar'?", "a" => ["Karena berharga", "Karena langsung hilang diculik pesulap setelah ditukarkan", "Karena bisa ditukar apa saja", "Karena dicetak pemerintah"], "correct" => "Karena langsung hilang diculik pesulap setelah ditukarkan"],
        ["q" => "Apa peran mesin cuci dalam misteri hilangnya satu sisi kaos kaki?", "a" => ["Sebagai tempat persembunyian", "Sebagai portal dimensi yang butuh tumbal", "Sebagai penghancur kain", "Sebagai tempat mencuci"], "correct" => "Sebagai portal dimensi yang butuh tumbal"]
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz gaje 666</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .btn-primary { background: linear-gradient(135deg, #9333ea 0%, #db2777 100%); transition: transform 0.2s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(147, 51, 234, 0.3); }
        .quiz-option { transition: all 0.2s ease; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- NAVIGATION -->
    <nav class="sticky top-0 z-50 glass border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="index.php?view=landing" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l-5.5 9h11L12 2zm0 2.84L14.54 9H9.46L12 4.84zM5.5 13l-3.5 6h11l-3.5-6h-4zm3.12 4l1.16-2h1.44l1.16 2H8.62zM18.5 13l-3.5 6h7l-3.5-6z"/></svg>
                </div>
                <span class="text-xl font-extrabold italic text-purple-900 tracking-tight">Quiz gaje 666</span>
            </a>
            <div class="flex items-center gap-4">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline text-sm font-bold text-slate-600"><?= htmlspecialchars($_SESSION['user']) ?></span>
                        <a href="index.php?view=logout" class="px-4 py-2 text-red-500 hover:bg-red-50 rounded-lg text-sm font-bold">Keluar</a>
                    </div>
                <?php else: ?>
                    <a href="index.php?view=login" class="px-6 py-2 bg-slate-900 text-white rounded-lg font-bold text-sm">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-6xl mx-auto w-full p-6">

        <?php if ($view === 'landing'): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center animate-in fade-in duration-700">
                <h1 class="text-6xl font-black text-slate-900 mb-6 tracking-tighter">Level Up <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500">Knowledge Gaje-mu</span></h1>
                <p class="text-lg text-slate-500 max-w-2xl mb-10">Uji pengetahuan dari teknologi 2025 sampai teori konspirasi paling nggak masuk akal sedunia.</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="index.php?view=login" class="btn-primary px-10 py-4 text-white rounded-2xl font-bold text-lg shadow-xl">Mulai Sekarang</a>
                    <a href="index.php?view=leaderboard" class="px-10 py-4 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold text-lg hover:bg-slate-50">Hall of Fame</a>
                </div>
            </div>

        <?php elseif ($view === 'login' || $view === 'signup'): ?>
            <div class="flex justify-center items-center py-10">
                <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden">
                    <div class="bg-gradient-to-br from-purple-600 to-indigo-700 p-8 text-white">
                        <h2 class="text-3xl font-bold"><?= $view === 'login' ? 'Selamat Datang' : 'Daftar Akun' ?></h2>
                        <p class="opacity-80">Siapkan otak gaje-mu untuk tantangan ini.</p>
                    </div>
                    <form method="POST" class="p-8 space-y-4">
                        <input type="hidden" name="action" value="<?= $view ?>">
                        <?php if($error): ?>
                            <div class="p-4 bg-red-50 text-red-600 text-sm rounded-xl border border-red-100 font-bold"><?= $error ?></div>
                        <?php endif; ?>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                            <input type="text" name="username" placeholder="Username" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                            <input type="password" name="password" placeholder="••••••••" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>
                        <button type="submit" class="w-full py-4 btn-primary text-white rounded-xl font-bold shadow-lg mt-2"><?= $view === 'login' ? 'Masuk' : 'Daftar' ?></button>
                        <p class="text-center text-sm text-slate-500 mt-4">
                            <?= $view === 'login' ? "Belum punya akun?" : "Sudah punya akun?" ?>
                            <a href="index.php?view=<?= $view === 'login' ? 'signup' : 'login' ?>" class="text-purple-600 font-bold hover:underline"><?= $view === 'login' ? 'Daftar' : 'Masuk' ?></a>
                        </p>
                    </form>
                </div>
            </div>

        <?php elseif ($view === 'dashboard'): ?>
            <?php if(!isset($_SESSION['user'])) { header("Location: index.php?view=login"); exit(); } ?>
            <div class="py-10">
                <!-- RESUME BANNER -->
                <div id="resume-container" class="hidden mb-10 p-6 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl text-white flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-2xl">
                             <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Lanjutkan Kuis?</h3>
                            <p class="opacity-90 text-sm">Ada progress yang tersimpan. Ingin lanjut bermain?</p>
                        </div>
                    </div>
                    <button onclick="handleResume()" class="px-8 py-3 bg-white text-purple-600 rounded-xl font-bold hover:bg-slate-50 transition-all shadow-md">Lanjutkan Sekarang →</button>
                </div>

                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Pilih Medan Perang</h2>
                        <p class="text-slate-500">Setiap kuis berisi 10 pertanyaan maut.</p>
                    </div>
                    <a href="index.php?view=leaderboard" class="p-4 bg-amber-50 rounded-2xl text-amber-600 hover:bg-amber-100 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3-.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- SKKM -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-2xl transition-all cursor-pointer group" onclick="startQuiz('skkm')">
                        <div class="w-14 h-14 bg-rose-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-rose-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-slate-900">SKKM</h3>
                        <p class="text-slate-500 text-sm mb-6 leading-relaxed">Seni dan Kebugaran Kesehatan Masyarakat: Senam & Fakta Sehat.</p>
                        <span class="text-purple-600 font-bold group-hover:translate-x-2 transition-transform inline-block">Mulai Kuis →</span>
                    </div>

                    <!-- CS -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-2xl transition-all cursor-pointer group" onclick="startQuiz('cs')">
                        <div class="w-14 h-14 bg-blue-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-slate-900">Ilmu Komputer</h3>
                        <p class="text-slate-500 text-sm mb-6 leading-relaxed">Computer Science & AI Trends relevant for 2025.</p>
                        <span class="text-purple-600 font-bold group-hover:translate-x-2 transition-transform inline-block">Mulai Kuis →</span>
                    </div>

                    <!-- GAJELAS -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-2xl transition-all cursor-pointer group" onclick="startQuiz('gajelas')">
                        <div class="w-14 h-14 bg-purple-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-purple-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-slate-900">Ilmu gajelas</h3>
                        <p class="text-slate-500 text-sm mb-6 leading-relaxed">Kumpulan teori konspirasi dan fakta nonsense yang bikin pusing.</p>
                        <span class="text-purple-600 font-bold group-hover:translate-x-2 transition-transform inline-block">Mulai Kuis →</span>
                    </div>
                </div>
            </div>

        <?php elseif ($view === 'quiz'): ?>
            <div id="quiz-container" class="max-w-2xl mx-auto py-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
                <div class="flex justify-between items-center mb-8">
                    <button onclick="exitQuiz()" class="text-slate-400 font-bold hover:text-red-500 flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Keluar
                    </button>
                    <button id="btn-back" onclick="goBack()" class="hidden text-purple-600 font-bold hover:bg-purple-50 px-4 py-2 rounded-xl flex items-center gap-1 transition-all">
                        ← Kembali
                    </button>
                </div>

                <div class="mb-10">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <span id="question-number" class="text-xs font-bold text-purple-600 uppercase tracking-widest">Pertanyaan 1 dari 10</span>
                            <h2 id="question-text" class="text-2xl font-bold text-slate-900 mt-2 leading-tight">Loading...</h2>
                        </div>
                        <span id="progress-percent" class="text-sm text-slate-400 font-mono font-bold">0%</span>
                    </div>
                    <div class="h-3 bg-slate-200 rounded-full overflow-hidden shadow-inner">
                        <div id="progress-bar" class="h-full bg-gradient-to-r from-purple-600 to-pink-500 transition-all duration-500" style="width: 10%"></div>
                    </div>
                </div>

                <div id="options-container" class="space-y-4">
                    <!-- Options injected by JS -->
                </div>
            </div>

            <!-- RESULTS SCREEN -->
            <div id="results-container" class="hidden max-w-md mx-auto py-10 text-center animate-in zoom-in duration-500">
                <div class="w-32 h-32 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </div>
                <h2 class="text-4xl font-black text-slate-900 mb-2">Kuis Selesai!</h2>
                <p id="result-summary" class="text-slate-500 mb-10 font-medium">Anda menjawab 0 dari 10 pertanyaan dengan benar.</p>
                <div class="bg-white p-8 rounded-3xl mb-10 border border-slate-100 shadow-xl">
                    <div id="final-score-percent" class="text-7xl font-black text-purple-600">0%</div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-3">Akurasi Akhir</div>
                </div>
                <div class="grid grid-cols-1 gap-3">
                    <a href="index.php?view=dashboard" class="py-4 btn-primary text-white rounded-2xl font-bold shadow-lg">Main Lagi</a>
                    <a href="index.php?view=leaderboard" class="py-4 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50">Lihat Peringkat</a>
                </div>
            </div>

        <?php elseif ($view === 'leaderboard'): ?>
            <div class="max-w-3xl mx-auto py-10 animate-in fade-in duration-500">
                <div class="flex items-center gap-4 mb-10">
                    <a href="index.php?view=dashboard" class="p-3 hover:bg-slate-100 rounded-xl text-slate-500 font-bold transition-all">←</a>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight">Hall of Fame</h2>
                </div>
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 text-slate-400 text-xs font-bold uppercase tracking-widest">
                                <tr>
                                    <th class="px-8 py-5">Rank & Player</th>
                                    <th class="px-8 py-5">Kategori</th>
                                    <th class="px-8 py-5 text-center">Skor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php
                                $lb = mysqli_query($conn, "SELECT * FROM leaderboard ORDER BY score DESC, played_at DESC LIMIT 20");
                                $i = 1;
                                if(mysqli_num_rows($lb) > 0):
                                    while($row = mysqli_fetch_assoc($lb)):
                                ?>
                                <tr class="hover:bg-purple-50/50 transition-colors">
                                    <td class="px-8 py-6 flex items-center gap-4">
                                        <span class="w-10 h-10 rounded-xl <?= $i==1 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center font-black text-sm"><?= $i++ ?></span>
                                        <span class="font-bold text-slate-800"><?= htmlspecialchars($row['username']) ?></span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold"><?= htmlspecialchars($row['category']) ?></span>
                                    </td>
                                    <td class="px-8 py-6 text-center font-black text-purple-600 text-lg"><?= $row['score'] ?>/10</td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400 italic">Belum ada skor tercatat.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <footer class="py-12 border-t border-slate-200 text-center mt-auto bg-white/50">
        <p class="text-slate-400 text-sm font-bold tracking-wide italic">© 2025 QUIZ GAJE 666. JANGAN TERLALU SERIUS.</p>
    </footer>

    <script>
        const QUESTIONS = <?= json_encode($questions) ?>;
        const storageKey = 'quiz_gaje_666_v3';
        let currentCat = '';
        let currentIndex = 0;
        let userAnswers = [];

        // Check for resume data on dashboard
        window.addEventListener('DOMContentLoaded', () => {
            const resumeContainer = document.getElementById('resume-container');
            if (resumeContainer) {
                const saved = localStorage.getItem(storageKey);
                if (saved) resumeContainer.classList.remove('hidden');
            }
        });

        function startQuiz(catId) {
            currentCat = catId;
            currentIndex = 0;
            userAnswers = [];
            localStorage.removeItem(storageKey);
            window.location.href = 'index.php?view=quiz&category=' + catId;
        }

        function handleResume() {
            const saved = localStorage.getItem(storageKey);
            if (saved) {
                const data = JSON.parse(saved);
                window.location.href = 'index.php?view=quiz&category=' + data.cat;
            }
        }

        // --- QUIZ ENGINE ---
        if (window.location.search.includes('view=quiz')) {
            const urlParams = new URLSearchParams(window.location.search);
            currentCat = urlParams.get('category');
            
            if (!currentCat || !QUESTIONS[currentCat]) {
                window.location.href = 'index.php?view=dashboard';
            }

            // Sync from storage
            const saved = localStorage.getItem(storageKey);
            if (saved) {
                const data = JSON.parse(saved);
                if (data.cat === currentCat) {
                    currentIndex = data.idx;
                    userAnswers = data.ans;
                }
            }
            
            window.addEventListener('load', renderQuestion);
        }

        function renderQuestion() {
            const qSet = QUESTIONS[currentCat];
            const q = qSet[currentIndex];
            
            document.getElementById('question-number').innerText = `Pertanyaan ${currentIndex + 1} dari 10`;
            document.getElementById('question-text').innerText = q.q;
            
            const progress = ((currentIndex + 1) / 10) * 100;
            document.getElementById('progress-bar').style.width = progress + '%';
            document.getElementById('progress-percent').innerText = Math.round(progress) + '%';
            
            const currentSelection = userAnswers[currentIndex];
            const optionsHtml = q.a.map(opt => `
                <button onclick="handleSelect('${opt.replace(/'/g, "\\'")}')" class="quiz-option w-full p-5 text-left bg-white border-2 ${currentSelection === opt ? 'border-purple-600 bg-purple-50 ring-2 ring-purple-100' : 'border-slate-100'} rounded-2xl hover:border-purple-500 flex justify-between items-center group">
                    <span class="text-lg font-bold ${currentSelection === opt ? 'text-purple-900' : 'text-slate-700'}">${opt}</span>
                    <div class="w-6 h-6 rounded-full border-2 ${currentSelection === opt ? 'bg-purple-600 border-purple-600' : 'border-slate-200'}">
                        ${currentSelection === opt ? '<svg class="w-full h-full text-white p-1" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>' : ''}
                    </div>
                </button>
            `).join('');
            
            document.getElementById('options-container').innerHTML = optionsHtml;
            document.getElementById('btn-back').classList.toggle('hidden', currentIndex === 0);

            // Save Progress
            localStorage.setItem(storageKey, JSON.stringify({
                cat: currentCat,
                idx: currentIndex,
                ans: userAnswers
            }));
        }

        function handleSelect(opt) {
            userAnswers[currentIndex] = opt;
            if (currentIndex < 9) {
                currentIndex++;
                renderQuestion();
            } else {
                finishQuiz();
            }
        }

        function goBack() {
            if (currentIndex > 0) {
                currentIndex--;
                renderQuestion();
            }
        }

        function exitQuiz() {
            if(confirm("Yakin ingin keluar? Progress kuis Anda akan tersimpan.")) {
                window.location.href = 'index.php?view=dashboard';
            }
        }

        function finishQuiz() {
            let score = 0;
            const qSet = QUESTIONS[currentCat];
            userAnswers.forEach((ans, i) => {
                if (ans === qSet[i].correct) score++;
            });

            const formData = new FormData();
            formData.append('action', 'submit_quiz');
            formData.append('score', score);
            formData.append('category', currentCat);

            fetch('index.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    localStorage.removeItem(storageKey);
                    document.getElementById('quiz-container').classList.add('hidden');
                    document.getElementById('results-container').classList.remove('hidden');
                    document.getElementById('result-summary').innerText = `Anda menjawab ${score} dari 10 pertanyaan dengan benar.`;
                    document.getElementById('final-score-percent').innerText = (score * 10) + '%';
                });
        }
    </script>
</body>
</html>