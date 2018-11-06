<p>Hvis du trykker på knappen under, velges rommet og turen går til nestemann. Denne handlingen kan ikke angres.<br/>
Valgt rom: <b><?php echo $rom->getNavn(); ?></b>.<br/>
Type: <b><?php echo $rom->getType()->getNavn(); ?></b>.</p>

<?php if(isset($ekstratekst)) {
    echo "<p>$ekstratekst</p>";
}
?>

<p><button class="btn btn-danger" onclick="velgRom()">VELG ROM</button></p>


<script>

    function velgRom() {
        $.ajax({
            type: 'POST',
            url: '?a=storhybel/velg',
            data: 'rom_id=' + '<?php echo $rom->getId(); ?>',
            method: 'POST',
            success: function (data) {
                $("#velg-modal").modal("hide");
                tilbakemelding(data);
                setTimeout(function(){
                    window.location.reload(1);
                }, 5000);
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function tilbakemelding(beskjed) {
        document.getElementById("success").style.display = "table";
        document.getElementById("tilbakemelding-text").innerHTML = beskjed;
    }
</script>


