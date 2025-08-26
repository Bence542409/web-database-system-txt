var count;

(async () => {
    const response = await fetch("id.txt");
    const data = await response.text();
    const lines = data.split("\n");
    count = lines.length / 4;
})();

function id_check() {
    if (document.getElementById("pw").value == 0)
    {
        alert("Helytelen azonosítószám");
    }
    else if (document.getElementById("pw").value <= count)
    {
        window.open("id.html?" + document.getElementById("pw").value)
    }
    else
    {
        alert("Nem található azonosítószám");
    }
}