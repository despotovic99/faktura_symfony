const $ = require('jquery');

let listaStavkiDiv;
let dodajStavkuBtn = $('#dodajStavkuFaktureBtn');

$(document).ready(function () {

    listaStavkiDiv = $('#lista-stavki');
    // listaStavkiDiv.append(dodajStavkuBtn);
    listaStavkiDiv.data('index', listaStavkiDiv.find('.stavka-panel').length)


    listaStavkiDiv.find('.stavka-panel').each(function () {
        dodajObrisiBtn($(this));
    });

});
dodajStavkuBtn.click(function (e) {
    e.preventDefault();
    console.log($('#stavke-fakture-container').data('proizvodiselect'))
    let prototip = `
                                     <tr>
                                        <td>
                                        ${$('#stavke-fakture-container').data('proizvodiselect')}
                                        </td>
                                        <td>
                                            <input type="number" class="stavka-kolicina-class form-control"
                                                   name="stavka-kolicina"
                                                   value="0">
                                        </td>
                                        <td class="stavka-jedinica-mere">0</td>
                                        <td class="stavka-cena">0</td>
                                        <td class="stavka-ukupno">0</td>
                                        <td class="btn-ukloni-stavku">
                                            <button type="button" class="btn btn-danger">X</button>
                                        </td>
                                    </tr>
        `;

    $('#stavke-table-body').append(prototip)
})

function kreirajNovuFormu() {
    let prototype = listaStavkiDiv.data('prototype');
    let index = listaStavkiDiv.data('index');


    let novaStavkaForm = prototype;
    novaStavkaForm = novaStavkaForm.replace(/__name__/g, index);


    listaStavkiDiv.data('index', index + 1);

    let $panel = $(` <li class="stavka-panel"></li>`);

    $panel.append(novaStavkaForm);

    dodajObrisiBtn($panel);
    listaStavkiDiv.append($panel);

}


function dodajObrisiBtn($panel) {

    let divObrisiStavkaBtn = $(`<div class="obrisi-stavka-btn" style="height: 40px"></div>`)
    let obrisiStavkaBtn = $(`<button class="btn btn-danger" style="height: 90%" >Obrisi </button>`);
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

$('input[name="formatStampe"]').click(() => {
    console.log($('input[name="formatStampe"]:checked').val());
    $('#form-stampanje').attr('action', $('input[name="formatStampe"]:checked').val())

});

$('.stavka-select-class').on('change', (e) => {
    let selectedOption = $(e.target).find(':selected');
    let jedinicaMereTd = $(e.target).parent().parent().find('.stavka-jedinica-mere')[0];
    let cenaTd = $(e.target).parent().parent().find('.stavka-cena')[0];
    let kolicinaTd = $(e.target).parent().parent().find('.stavka-kolicina-class')[0]
    let ukupnoTd = $(e.target).parent().parent().find('.stavka-ukupno')[0]

    jedinicaMereTd.innerText = selectedOption.data('jm');
    cenaTd.innerText = selectedOption.data('cenapojedinici');
    let cena = selectedOption.data('cenapojedinici');

    ukupnoTd.innerText = cena * kolicinaTd.value;
});

$('.stavka-kolicina-class').keyup((e) => {

    let inputKolicina = $(e.target)[0].value;

    let redRoditelj = $(e.target).parent().parent();
    let cenaTd = redRoditelj.find('.stavka-cena')[0];
    let ukupnoTd = redRoditelj.find('.stavka-ukupno')[0];


    let cena = parseFloat(cenaTd.innerText)
    try {
        ukupnoTd.innerText = cena * inputKolicina
    } catch (exception) {
        ukupnoTd.innerText = 0;
    }
})
