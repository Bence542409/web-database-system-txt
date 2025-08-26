<?php
session_start();

$file = __DIR__ . "/id.txt";

// AJAX kérés az adatok betöltésére
if (isset($_GET['load_id'])) {
    $loadId = trim($_GET['load_id']);
    $response = [
        'found' => false,
        'tartalom' => '',
        'tipus' => '',
        'hely' => ''
    ];

    if ($loadId !== '') {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $loadIdLtrim = ltrim($loadId, "0");

        for ($i = 0; $i < count($lines); $i += 4) {
            $currentId = ltrim($lines[$i], "0");

            if ($currentId === $loadIdLtrim) {
                $response['found'] = true;
                $response['tartalom'] = $lines[$i + 1];
                $response['tipus'] = $lines[$i + 2];
                $response['hely'] = $lines[$i + 3];
                break;
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Módosítás gomb
if (isset($_POST['modify'])) {
    $modifyId = trim($_POST['modify_id'] ?? '');
    $tartalom = trim($_POST['tartalom'] ?? '');
    $tipus = trim($_POST['tipus'] ?? '');
    $hely = trim($_POST['hely'] ?? '');

    if ($modifyId === '' || $tartalom === '' || $tipus === '' || $hely === '') {
        $_SESSION['msg'] = [
            'type' => 'error',
            'text' => "Sikertelen művelet"
        ];
    } else {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $modifyIdLtrim = ltrim($modifyId, "0");
        $found = false;

        for ($i = 0; $i < count($lines); $i += 4) {
            $currentId = ltrim($lines[$i], "0");

            if ($currentId === $modifyIdLtrim) {
                $found = true;
                $lines[$i + 1] = $tartalom;
                $lines[$i + 2] = $tipus;
                $lines[$i + 3] = $hely;
                break;
            }
        }

        if ($found) {
            file_put_contents($file, implode("\n", $lines));
            $_SESSION['msg'] = [
                'type' => 'success',
                'text' => "Azonosító sikeresen módosítva (ID: {$modifyId})"
            ];
        } else {
            $_SESSION['msg'] = [
                'type' => 'error',
                'text' => "Nem létezik a megadott azonosítószám"
            ];
        }
    }

    $_SESSION['redirect'] = true;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azonosító módosítása - Garázs Szelektáló Rendszer v3.1</title>
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
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
        }

        /* --- Container --- */
        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 420px;
        }

        h1 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: #333;
            text-align: center;
        }

        /* --- Form elemek --- */
        form label {
            display: block;
            margin: 0.6rem 0 0.3rem;
            color: #444;
            font-size: 0.95rem;
        }

        form input {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        button {
            margin-top: 1rem;
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            background: #ff9900;
            color: white;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: #cc7a00;
        }

        /* --- Wrapper --- */
        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        /* --- Üzenetek --- */
        .msg {
            margin-top: 20px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            padding: 0.8rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            font-size: 0.95rem;
        }

        .msg.success {
            background: #e6f4ea;
            color: #256029;
            border: 1px solid #b6dfb9;
        }

        .msg.error {
            background: #fdecea;
            color: #8a1c1c;
            border: 1px solid #f5c2c0;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <h1>Azonosító módosítása</h1>

        <form method="post" id="modifyForm">
            <label for="modify_id">Azonosító:</label>
            <input type="text" id="modify_id" name="modify_id" autocomplete="off">

            <label for="tartalom">Tárolóban található elemek:</label>
            <input type="text" id="tartalom" name="tartalom">

            <label for="tipus">Tároló típusa:</label>
            <input type="text" id="tipus" name="tipus">

            <label for="hely">Tároló helye:</label>
            <input type="text" id="hely" name="hely">

            <button type="submit" name="modify">Azonosító módosítása</button>
        </form>
    </div>

    <div id="ajaxMsg"></div>

    <?php if (!empty($_SESSION['msg'])): ?>
        <div class="msg <?php echo $_SESSION['msg']['type']; ?>">
            <?php echo $_SESSION['msg']['text']; ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>
</div>

<script>
const modifyIdInput = document.getElementById('modify_id');
const tartalomInput = document.getElementById('tartalom');
const tipusInput = document.getElementById('tipus');
const helyInput = document.getElementById('hely');
const ajaxMsg = document.getElementById('ajaxMsg');

let timeout = null;

modifyIdInput.addEventListener('input', () => {
    clearTimeout(timeout);

    timeout = setTimeout(() => {
        const id = modifyIdInput.value.trim();
        if (id === '') {
            tartalomInput.value = '';
            tipusInput.value = '';
            helyInput.value = '';
            ajaxMsg.innerHTML = '';
            return;
        }

        fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?load_id=${encodeURIComponent(id)}`)
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    tartalomInput.value = data.tartalom;
                    tipusInput.value = data.tipus;
                    helyInput.value = data.hely;
                    ajaxMsg.innerHTML = '';
                } else {
                    tartalomInput.value = '';
                    tipusInput.value = '';
                    helyInput.value = '';
                    ajaxMsg.innerHTML = '<div class="msg error">Nem létezik a megadott azonosítószám</div>';
                }
            });
    }, 300); // 300ms késleltetés
});
</script>
</body>
</html>
