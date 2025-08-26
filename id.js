//let id = 0001;
var a = location.href; 
let id = a.substring(a.indexOf("?")+1);
    (async () => {
  const response = await fetch("id.txt");
  const data = await response.text();
  const lines = data.split("\n");
  document.getElementById('header').innerHTML = lines[(id - 1) * 4]
  document.getElementById('picture').innerHTML = "<img src='picture/"+lines[(id - 1) * 4]+".png' height='300px' width='500px' style='border: 1px solid black;'>"
  document.getElementById('id').innerHTML = lines[(id - 1) * 4]
  document.getElementById('content').innerHTML = lines[(id - 1) * 4 + 1]
  document.getElementById('type').innerHTML = lines[(id - 1) * 4 + 2]
  document.getElementById('location').innerHTML = lines[(id - 1) * 4 + 3]
  document.getElementById('barcode').innerHTML = "<a href='barcode/"+lines[(id - 1) * 4]+".png'>QR KÓD</a>"
})();

let file = "id.txt";

function makeRequest(url) {
    return new Promise(function (resolve, reject) {
        let xhr = new XMLHttpRequest();
        let rand = Math.floor(Math.random() * (99999 - 11111) + 11111);
        let newurl = url+"?v="+rand;
        xhr.open("HEAD", newurl);
        xhr.onload = function () {
            if (this.status >= 200 && this.status < 300) {
                resolve(xhr.getResponseHeader("Last-Modified"));
            } else {
                reject({
                    status: this.status,
                    statusText: xhr.statusText
                });
            }
        };
        xhr.onerror = function () {
            reject({
                status: this.status,
                statusText: xhr.statusText
            });
        };
        xhr.send();
    });
}

document.onload = rando();

async function rando(){
    let result = await makeRequest(file);
    let time = Date.parse(result)/1000;
    var utcSeconds = time;
    var d = new Date(0); // The 0 there is the key, which sets the date to the epoch
    document.getElementById('modified').innerHTML = new Date(d.setUTCSeconds(utcSeconds)).toLocaleString('hu-hu', { timeZone: 'Europe/Budapest', weekday:"long", year:"numeric", month:"short", day:"numeric", hour:"2-digit", minute:"2-digit"})
}

