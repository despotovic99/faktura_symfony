const $ = require('jquery');

$('.stavka-select-class').on('change', (e) => {
    let selectedOption = $(e.target).find(':selected');

    let trRoditelj = selectedOption.parents('tr');

    let jedinicaMereTd = trRoditelj.find('.stavka-jedinica-mere');
    let cenaTd = trRoditelj.find('.stavka-cena')
    let kolicinaTd = trRoditelj.find('.stavka-kolicina-class')
    let ukupnoTd = trRoditelj.find('.stavka-ukupno')

    jedinicaMereTd.html(selectedOption.data('jm'));
    cenaTd.html(selectedOption.data('cenapojedinici'))

    let cena = selectedOption.data('cenapojedinici');
    let kolicina = parseFloat(kolicinaTd.val())

    ukupnoTd.html(cena * kolicina)
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

function brisanjeStavke(e) {
    console.log(e)
}

$('#dodajStavkuFaktureBtn').click(function (e) {
    e.preventDefault();
    let dugmeObrisi = $(`<button type="button" class="btn btn-danger btn-obrisi-stavku">X</button>`);
    dugmeObrisi.click(brisanjeStavke);
    console.log(dugmeObrisi[0].outerHTML)
    let prototip = $(`<tr>
                                        <td>
                                        ${$('#stavke-fakture-template-proizvodi').html()}
                                        </td>
                                        <td>
                                            <input type="number" class="stavka-kolicina-class form-control"
                                                   name="stavka-kolicina"
                                                   value="0">
                                        </td>
                                        <td class="stavka-jedinica-mere">0</td>
                                        <td class="stavka-cena">0</td>
                                        <td class="stavka-ukupno">0</td>
                                    </tr>
        `);
    let td = $(`<td></td>`);
    td.append(dugmeObrisi);
    prototip.append(td);
    $('#stavke-table-body tr:last').before(prototip);
})

$('.btn-obrisi-stavku').on('click', brisanjeStavke);

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


