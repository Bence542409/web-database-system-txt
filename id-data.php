<?php
$file = __DIR__ . "/id.txt";

// Ha a fájl nem létezik, üres adatok
if (!file_exists($file)) {
    die("Az id.txt fájl nem található!");
}

// Fájl beolvasása soronként
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$totalIds = count($lines) / 4; // minden ID 4 sorból áll

$ures = 0;
$ismeretlen = 0;

// Sorok feldolgozása 4-es blokkokban
for ($i = 0; $i < count($lines); $i += 4) {
    $tartalom = $lines[$i + 1] ?? '';
    if ($tartalom === '[ÜRES]') {
        $ures++;
    } elseif ($tartalom === '[NINCS INFORMÁCIÓ]') {
        $ismeretlen++;
    }
}

// Utolsó módosítás dátuma
$lastModified = date("Y-m-d H:i:s", filemtime($file));
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adatok - Garázs Szelektáló Rendszer v3.1</title>
    <link rel="stylesheet" type="text/css" href="1.css">
    <link rel="shortcut icon" href="favicon.ico" />
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <style>  
    /* --- Alap beállítások --- */
    * {
        box-sizing: border-box;
    }
            
    body {
        font-family: 'Roboto', sans-serif;
        padding: 2rem;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        margin: 0;
    }

    .container {
        background: #fff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 420px;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 1.5rem;
        font-size: 1.4rem;
    }

    p {
        font-size: 0.95rem;
        margin: 0.6rem 0;
        color: #333;
        line-height: 1.5;
    }

</style>

</head>
<body>
    <div class="container">
        <h1>Adatok</h1>
        <p><strong>Eltárolt azonosítók száma:</strong> <?php echo (int)$totalIds; ?></p>
        <p><strong>Üres tárolók száma:</strong> <?php echo $ures; ?></p>
        <p><strong>Ismeretlen tárolók száma:</strong> <?php echo $ismeretlen; ?></p>
        <p><strong>Módosítás:</strong> <?php echo $lastModified; ?></p>
    </div>
</body>
</html>
