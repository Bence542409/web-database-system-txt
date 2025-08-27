<?php
session_start();

// id.txt beolvasása
$file = __DIR__ . "/id.txt";
$entries = [];

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    for ($i = 0; $i < count($lines); $i += 4) {
        $id = $lines[$i] ?? '';
        $tartalom = $lines[$i + 1] ?? '';
        $entries[] = ['id' => str_pad($id, 4, "0", STR_PAD_LEFT), 'tartalom' => $tartalom];
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kulcsszó szerinti keresés - Garázs Szelektáló Rendszer v3.1</title>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
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
#search-container {
    padding: 1rem;
    background: #fff;
}
#search {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
}
.entry {
    padding: 0.8rem;
    border-bottom: 1px solid #ddd;
    cursor: pointer;
    transition: background 0.2s;
}
.entry:hover {
    background: #f0f8ff;
}
.msg {
    position: fixed;
    top: 1rem;
    left: 50%;
    transform: translateX(-50%);
    max-width: 90%;
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    font-size: 0.95rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    z-index: 1000;
}
.msg.success { background: #e6f4ea; color: #256029; border: 1px solid #b6dfb9; }
.msg.error { background: #fdecea; color: #8a1c1c; border: 1px solid #f5c2c0; }
</style>
</head>
<body>
<header>Kulcsszó szerinti keresés</header>
<div id="search-container">
    <input type="text" id="search" placeholder="Keresés tartalom alapján...">
</div>
<div id="list">
    <?php foreach ($entries as $e): ?>
        <div class="entry" data-id="<?php echo $e['id']; ?>">
            <?php echo htmlspecialchars($e['tartalom']); ?>
        </div>
    <?php endforeach; ?>
</div>

<div id="msg-container"></div>

<script>
const searchInput = document.getElementById('search');
const list = document.getElementById('list');
const entries = Array.from(list.getElementsByClassName('entry'));
const msgContainer = document.getElementById('msg-container');

function showMessage(text, type = 'error') {
    const div = document.createElement('div');
    div.className = 'msg ' + type;
    div.textContent = text;
    msgContainer.appendChild(div);
    setTimeout(() => {
        div.style.transition = "opacity 0.5s";
        div.style.opacity = "0";
        setTimeout(() => div.remove(), 500);
    }, 2000);
}

// Keresés
searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase();
    let anyVisible = false;
    entries.forEach(entry => {
        const text = entry.textContent.toLowerCase();
        const visible = text.includes(query);
        entry.style.display = visible ? '' : 'none';
        if (visible) anyVisible = true;
    });
    if (!anyVisible && query !== '') {
        showMessage("Nincs találat a keresésre", "error");
    }
});

// Kattintás az entry-re
entries.forEach(entry => {
    entry.addEventListener('click', () => {
        const id = entry.getAttribute('data-id');
        window.location.href = 'id.html?' + id;
    });
});
</script>
</body>
</html>
