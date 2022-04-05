const $ = require('jquery');

var listaStavkiDiv;
var dodajStavkuBtn = $('#dodajStavkuFaktureBtn');

$(document).ready(function () {

    listaStavkiDiv = $('#lista-stavki');
    // listaStavkiDiv.append(dodajStavkuBtn);
    listaStavkiDiv.data('index', listaStavkiDiv.find('.stavka-panel').length)


    listaStavkiDiv.find('.stavka-panel').each(function () {
        dodajObrisiBtn($(this));
    });

    dodajStavkuBtn.click(function (e) {
        e.preventDefault();
        kreirajNovuFormu();
    })
});

function kreirajNovuFormu() {
    var prototype = listaStavkiDiv.data('prototype');
    var index = listaStavkiDiv.data('index');


    var novaStavkaForm = prototype;
    novaStavkaForm = novaStavkaForm.replace(/__name__/g, index);


    listaStavkiDiv.data('index', index + 1);

    var $panel = $(` <li class="stavka-panel"></li>`);

    $panel.append(novaStavkaForm);

    dodajObrisiBtn($panel);
    listaStavkiDiv.append($panel);

}


function dodajObrisiBtn($panel) {

    var divObrisiStavkaBtn = $(`<div class="obrisi-stavka-btn" style="height: 40px"></div>`)
    var obrisiStavkaBtn = $(`<button class="btn btn-danger" style="height: 90%" >Obrisi </button>`);
    divObrisiStavkaBtn.append(obrisiStavkaBtn);
    obrisiStavkaBtn.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.stavka-panel').slideUp(1000, function () {
            $(this).remove();
        })
    });

    $panel.append(divObrisiStavkaBtn);
}


$("#btn-stapmanje-dialog").click(function () {
    $('.body-container').hide();
  $('#stampanje-dialog').show()
});

$("#btn-otkazi-stampanje").click(function () {
    $('#stampanje-dialog').hide();
  $('.body-container').show()
});

$('input[name="formatStampe"]').click(()=>{
    console.log($('input[name="formatStampe"]:checked').val());
    $('#form-stampanje').attr('action',$('input[name="formatStampe"]:checked').val())

});
