const $ = require('jquery');

var listaStavkiDiv;
var dodajStavkuBtn = $(`<button class="btn btn-success">Dodaj stavku </button>`);

$(document).ready(function () {

    listaStavkiDiv = $('#lista-stavki');
    listaStavkiDiv.append(dodajStavkuBtn);
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

    dodajStavkuBtn.before($panel);
}


function dodajObrisiBtn($panel) {

    var obrisiStavkaBtn = $(`<button class="btn btn-danger">Obrisi stavku </button>`);

    obrisiStavkaBtn.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.stavka-panel').slideUp(1000, function () {
            $(this).remove();
        })
    });

    $panel.append(obrisiStavkaBtn);
}
