<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tároló adatai</title>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
<link rel="shortcut icon" href="favicon.ico" />
<style>
* { box-sizing: border-box; }
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background: #f5f5f5;
}
header {
    padding: 1rem;
    background: #0077cc;
    color: white;
    text-align: center;
    font-size: 1.3rem;
}
.container {
    max-width: 800px;
    margin: 1rem auto;
    padding: 1rem;
}
.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 1rem;
    margin-bottom: 1rem;
}
.card img {
    width: 100%;
    height: auto;
    border: 1px solid #ccc;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
    height: 400px;
}
/* Telefonon kisebb magasság */
@media (max-width: 768px) {
    .card img {
        height: 200px;
    }
}
.card h2 {
    margin: 0.5rem 0;
    color: #0077cc;
    text-align: center;
}
.card a {
    display: inline-block;
    margin-top: 0.5rem;
    text-decoration: none;
    color: white;
    background: #0077cc;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: background 0.2s;
}
.card a:hover {
    background: #005fa3;
}
#barcode a {
    display: inline-block;
    padding: 0.6rem 1.2rem;
    background: #0077cc;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s;
}
.info-table {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    background: #fefefe;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    margin: 1rem auto 0 auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.info-table .row {
    display: flex;
    justify-content: space-between;
    padding: 0.4rem 0.6rem;
    border-bottom: 1px solid #eee;
}
.info-table .row:last-child {
    border-bottom: none;
}
.info-table .label {
    font-weight: 600;
    color: #0077cc;
    flex: 1;
    text-align: left;
}
.info-table .value {
    flex: 2;
    text-align: right;
    font-weight: 500;
    color: #333;
}
#barcode a:hover {
    background: #005fa3;
}
</style>
</head>
<body>
<header>Tároló adatai</header>
<div class="container">
    <div class="card">
        <?php
        // --- PHP adatbetöltés ---
        $rawQuery = $_SERVER['QUERY_STRING'] ?? '';
        $idStr = '';

        // Ha ?id=... van
        if (isset($_GET['id']) && $_GET['id'] !== '') {
            $idStr = (string)$_GET['id'];
        } elseif ($rawQuery !== '') {
            // ha pl. ?0001 van
            if (strpos($rawQuery, '=') !== false) {
                parse_str($rawQuery, $qsArr);
                if (!empty($qsArr['id'])) {
                    $idStr = (string)$qsArr['id'];
                } else {
                    foreach ($qsArr as $v) { $idStr = (string)$v; break; }
                }
            } else {
                $idStr = $rawQuery;
            }
        }

        // csak számjegyek
        $idStr = preg_replace('/\D/', '', $idStr);
        $idNum = (int)$idStr;

        $filePath = __DIR__ . '/id.txt';
        $pictureDir = __DIR__ . '/picture/';
        $barcodeDir = __DIR__ . '/barcode/';

        if (!file_exists($filePath)) {
            echo "<h2>Az id.txt fájl nem található</h2>";
        } else {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $total = intdiv(count($lines), 4);

            if ($idNum <= 0 || $idNum > $total) {
                echo "<h2>Érvénytelen azonosító</h2>";
            } else {
                $index = ($idNum - 1) * 4;
                $idValue = $lines[$index]; 
                $header = htmlspecialchars($idValue, ENT_QUOTES, 'UTF-8');
                $content = htmlspecialchars($lines[$index + 1] ?? '', ENT_QUOTES, 'UTF-8');
                $type = htmlspecialchars($lines[$index + 2] ?? '', ENT_QUOTES, 'UTF-8');
                $location = htmlspecialchars($lines[$index + 3] ?? '', ENT_QUOTES, 'UTF-8');

                // Címsor
                echo "<h2>{$header}</h2>";

                // Kép
                $picFile = $pictureDir . $idValue . ".png";
                if (file_exists($picFile)) {
                    $imgSrc = "picture/" . rawurlencode($idValue) . ".png";
                    echo "<div id='picture'><img src='{$imgSrc}' alt='Tároló kép'></div>";
                } else {
                    echo "<div id='picture' style='margin:0 auto; text-align:center;'>
                            <div style='height:400px; display:flex; align-items:center; justify-content:center; color:#999; border:1px dashed #ccc; border-radius:8px; background:#fafafa;'>
                                Nincs feltöltött kép
                            </div>
                          </div>";
                }

                // Info táblázat
                echo "<div class='info-table'>";
                echo "<div class='row'><div class='label'>ID:</div><div class='value'>{$header}</div></div>";
                echo "<div class='row'><div class='label'>Tartalom:</div><div class='value'>{$content}</div></div>";
                echo "<div class='row'><div class='label'>Típus:</div><div class='value'>{$type}</div></div>";
                echo "<div class='row'><div class='label'>Hely:</div><div class='value'>{$location}</div></div>";

                $mtime = filemtime($filePath);
                $modifiedStr = $mtime ? date("Y-m-d H:i", $mtime) : '—';
                echo "<div class='row'><div class='label'>Módosítva:</div><div class='value'>{$modifiedStr}</div></div>";
                echo "</div>";

                // QR kód
                $qrFile = $barcodeDir . $idValue . ".png";
                if (file_exists($qrFile)) {
                    $qrHref = "barcode/" . rawurlencode($idValue) . ".png";
                    echo "<div style='text-align:center; margin-top:1rem;' id='barcode'><a href='{$qrHref}'>QR KÓD</a></div>";
                } else {
                    echo "<div style='text-align:center; margin-top:1rem; color:#666;' id='barcode'>QR kód nem található</div>";
                }
            }
        }
        ?>
    </div>
</div>
</body>
</html>
