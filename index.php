<?php
session_start();

function safe(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$csrfToken = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

$errors = [];
$success = false;
$name = '';
$email = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Virheellinen lomaketunniste. Yritä uudelleen.';
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $errors[] = 'Nimi on pakollinen.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Anna voimassa oleva sähköposti.';
    }

    if ($message === '') {
        $errors[] = 'Viesti on pakollinen.';
    }

    if (empty($errors)) {
        $to = 'oma@email.com';
        $subject = 'Yhteydenottolomake';
        $body = "Nimi: $name\nSähköposti: $email\n\nViesti:\n$message\n";
        $headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";

        $success = true;
        $name = '';
        $email = '';
        $message = '';

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $csrfToken = $_SESSION['csrf_token'];
    }
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Omat nettisivut</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="custom-header text-center py-4">
    <div class="container d-flex flex-column align-items-center">
        <div class="d-flex align-items-center gap-3 mb-3">
            <img src="" alt="Profiilikuva" class="profile-img rounded-circle">
            <h1 class="h3 mb-0">Tuukka Heikkinen</h1>
        </div>

        <nav class="d-flex flex-wrap justify-content-center gap-2">
            <a class="btn btn-light rounded-pill px-3 text-dark" href="#etusivu">Etusivu</a>
            <a class="btn btn-light rounded-pill px-3 text-dark" href="#tietoa">Tietoa minusta</a>
            <a class="btn btn-light rounded-pill px-3 text-dark" href="#yhteys">Ota yhteyttä</a>
        </nav>
    </div>
</header>

<main class="container my-4">
    <section id="etusivu" class="bg-dark text-white rounded-3 p-4 mb-4">
        <h2>Tervetuloa</h2>
        <p>Olen opiskelija ja kiinnostunut ohjelmoinnista, autoista ja kuntosaliharjoittelusta.</p>
        <button class="btn btn-danger rounded-pill px-4">Paina tästä</button>
    </section>

    <section id="tietoa" class="row row-cols-1 row-cols-md-3 g-3 mb-4">
        <div class="col">
            <div class="card h-100 bg-black text-white border-primary">
                <div class="card-body">
                    <h2 class="card-title h5">Harrastukset</h2>
                    <ul class="mb-0">
                        <li>Kuntosali</li>
                        <li>Autot</li>
                        <li>Pelaaminen</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 bg-black text-white border-primary">
                <div class="card-body">
                    <h2 class="card-title h5">Koulutus</h2>
                    <p class="mb-0">Tieto- ja viestintätekniikan opiskelija</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 bg-black text-white border-primary">
                <div class="card-body">
                    <h2 class="card-title h5">Osaamiset</h2>
                    <p class="mb-0">HTML, CSS, C#, Visual Studio</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-dark text-white rounded-3 p-4 mb-4">
        <h2>Työnäytteet</h2>
        <table class="table table-borderless text-white mb-0">
            <thead class="bg-black">
                <tr>
                    <th class="text-danger">Projekti</th>
                    <th class="text-danger">Kieli</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pizzalaskuri</td>
                    <td>C#</td>
                </tr>
                <tr>
                    <td>Nettisivu</td>
                    <td>HTML/CSS</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="bg-dark text-white rounded-3 p-4 mb-4">
        <h2>Video</h2>
        <div class="ratio ratio-16x9">
            <iframe src="https://www.youtube.com/watch?v=KrIkx_MYvxk" title="YouTube video" allowfullscreen></iframe>
        </div>
    </section>

    <section id="yhteys" class="bg-dark text-white rounded-3 p-4 mb-4">
        <h2>Ota yhteyttä</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">Viesti lähetettiin onnistuneesti.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= safe($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="mt-3">
            <input type="hidden" name="csrf_token" value="<?= safe($csrfToken) ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold" for="name">Nimi:</label>
                <input id="name" name="name" type="text" class="form-control" value="<?= safe($name) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="email">Sähköposti:</label>
                <input id="email" name="email" type="email" class="form-control" value="<?= safe($email) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="message">Viesti:</label>
                <textarea id="message" name="message" class="form-control" rows="5" required><?= safe($message) ?></textarea>
            </div>

            <button class="btn btn-danger rounded-pill px-4" type="submit">Lähetä</button>
        </form>
    </section>
</main>

<footer class="text-center py-3 bg-footer">
    <p class="mb-0">2026 Omat sivut</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>