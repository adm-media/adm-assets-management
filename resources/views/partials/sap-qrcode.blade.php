<div class="panel panel-default" style="margin-top: 15px;">
    <div class="panel-heading text-center">
        <strong>QR Code SAP</strong>
    </div>
    <div class="panel-body text-center">
        <input type="text"
            id="qrcode_value"
            class="form-control text-center"
            value="{{ $asset->_snipeit_sap_code_47 ?? 'Nessun valore' }}"
            readonly
            style="max-width: 240px; margin: 0 auto 10px auto; font-weight: bold;">
        <img id="qrcode_image"
            src=""
            alt="QR Code SAP"
            style="display:none; max-width: 140px; margin: 0 auto;">
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var inputValue = document.getElementById('qrcode_value').value;
        if (inputValue.trim() !== "" && inputValue !== "Nessun valore") {
            generateQRCode();
        }
    });

    function generateQRCode() {
        var inputValue = document.getElementById('qrcode_value').value;
        var qrImage = document.getElementById('qrcode_image');

        if (inputValue.trim() !== "" && inputValue !== "Nessun valore") {
            qrImage.src = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(inputValue);
            qrImage.style.display = "block";
        } else {
            qrImage.style.display = "none";
        }
    }
</script>
