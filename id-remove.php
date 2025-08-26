<?php
session_start();

$file = __DIR__ . "/id.txt";

// Ha POST űrlap érkezett
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = trim($_POST['delete_id'] ?? '');

    if ($deleteId === '') {
        $_SESSION['msg'] = ['type' => 'error', 'text' => "Failed operation"];
    } else {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $deleteId = ltrim($deleteId, "0");

        $found = false;
        $newLines = [];
        for ($i = 0; $i < count($lines); $i += 4) {
            $currentId = ltrim($lines[$i], "0");
            if ($currentId === $deleteId) {
                $found = true;
                continue;
            } else {
                for ($j = 0; $j < 4 && ($i+$j) < count($lines); $j++) {
                    $newLines[] = $lines[$i + $j];
                }
            }
        }

        if ($found) {
            file_put_contents($file, implode("\n", $newLines));
            $_SESSION['msg'] = ['type' => 'success', 'text' => "Record succesfully deleted (ID: {$deleteId})"];
        } else {
            $_SESSION['msg'] = ['type' => 'error', 'text' => "Record not found"];
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
    <title>Delete a record - TXT Database System</title>
    <link rel="stylesheet" type="text/css" href="1.css">
    <link rel="shortcut icon" href="favicon.ico" />
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <style>
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
        }
        button {
            margin-top: 1rem;
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            background: #cc0000;
            color: white;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #a80000;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
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
            <h1>Delete a record</h1>
            <form method="post">
                <label for="delete_id">ID:</label>
                <input type="text" id="delete_id" name="delete_id">
                <button type="submit">Delete a record</button>
            </form>
        </div>

        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="msg <?php echo $_SESSION['msg']['type']; ?>">
                <?php echo $_SESSION['msg']['text']; ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['redirect'])): ?>
        <script>
            setTimeout(() => {
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>";
            }, 3000);
        </script>
        <?php unset($_SESSION['redirect']); ?>
    <?php endif; ?>
</body>
</html>
