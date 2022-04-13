const $ = require("jquery");
$("#btn-stapmanje-dialog").click(function () {
    $('.body-container').hide();
    $('#stampanje-dialog').show()
});

$("#btn-otkazi-stampanje").click(function () {
    $('#stampanje-dialog').hide();
    $('.body-container').show()
});

$('input[name="formatStampe"]').click(() => {
    console.log($('input[name="formatStampe"]:checked').val());
    $('#form-stampanje').attr('action', $('input[name="formatStampe"]:checked').val())

});