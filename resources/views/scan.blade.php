<h2>Scan QR Absensi</h2>

<div id="reader" style="width:300px;"></div>

<form id="form" action="/absen" method="POST">
    @csrf
    <input type="hidden" name="nim" id="nim">
</form>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
function onScanSuccess(decodedText, decodedResult) {
    alert("QR terbaca: " + decodedText);

    document.getElementById('nim').value = decodedText;
    document.getElementById('form').submit();
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: 250 }
);

html5QrcodeScanner.render(onScanSuccess);
</script>