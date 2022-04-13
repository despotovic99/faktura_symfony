const $ = require('jquery');

function azurirajVrednostiReda(e) {
    let selectedOption = $(e.target).find(':selected');

    let trRoditelj = selectedOption.parents('tr');

    let jedinicaMereTd = trRoditelj.find('.stavka-jedinica-mere');
    let cenaTd = trRoditelj.find('.stavka-cena')
    let kolicinaTd = trRoditelj.find('.stavka-kolicina-class')
    let ukupnoTd = trRoditelj.find('.stavka-iznos')

    jedinicaMereTd.html(selectedOption.data('jm'));
    cenaTd.html(selectedOption.data('cenapojedinici'))

    let cena = selectedOption.data('cenapojedinici');
    let kolicina = parseFloat(kolicinaTd.val())

    ukupnoTd.html((cena * kolicina).toFixed(2))
    azurirajIznosUkupnoNaFakturi()
}

function azurirajIznosURedu(e) {
    let inputKolicina = $(e.target).val();

    let redRoditelj = $(e.target).parents('tr');
    let cenaTd = redRoditelj.find('.stavka-cena');
    let ukupnoTd = redRoditelj.find('.stavka-iznos');

    let cena = parseFloat(cenaTd.html())

    try {
        ukupnoTd.html((cena * inputKolicina).toFixed(2));
    } catch (exception) {
        ukupnoTd.html(0)
    }finally {
        azurirajIznosUkupnoNaFakturi()
    }

}

function azurirajIznosUkupnoNaFakturi(){

    let stavke = $('.stavka-iznos');
    let ukupnaSuma=0;
    for(let i=0 ;i<stavke.length;i++){
        ukupnaSuma+=parseFloat(stavke[i].innerText);
    }
    $('#ukupan-iznos-fakture').html(ukupnaSuma.toFixed(2))

}

function brisanjeStavke(e) {
    e.preventDefault()
    let tr = $(e.target).parents('tr');
    tr.remove()
    azurirajIznosUkupnoNaFakturi();
}

$('.stavka-select-class').on('change', azurirajVrednostiReda);

$('.stavka-kolicina-class').keyup(azurirajIznosURedu)

$('.btn-obrisi-stavku').on('click', brisanjeStavke);

$('#dodajStavkuFaktureBtn').click(function (e) {
    e.preventDefault();

    let indeksStavke = parseFloat($('#stavke-table-body').attr('brojStavki'))+1;
    $('#stavke-table-body').attr('brojStavki',indeksStavke);
    let prototip = $(`<tr><input type="hidden" name="stavke[${indeksStavke}][stavka_id]"></tr>`);

    let td = $(`<td></td>`);
    let selectProizvodi = $('#stavke-fakture-template-proizvodi').find('select').clone();
    selectProizvodi.attr('name',`stavke[${indeksStavke}][stavka_proizvod]`);
    selectProizvodi.change(azurirajVrednostiReda)
    td.append(selectProizvodi);
    prototip.append(td);

    td = $(`<td></td>`);
    let inputKolicina = $(` <input type="number" class="stavka-kolicina-class form-control"
                                                   name="stavke[${indeksStavke}][kolicina]"
                                                   value="0"
                                                   min="0"
                                                   oninput="validity.valid||(value='')">`)
    inputKolicina.keyup(azurirajIznosURedu);
    td.append(inputKolicina);
    prototip.append(td);

    let tdJedinicaMere = $(`<td class="stavka-jedinica-mere">0</td>`);
    prototip.append(tdJedinicaMere);

    let tdStavkaCena = $(`<td class="stavka-cena">0</td>`);
    prototip.append(tdStavkaCena);

    let tdStavkaUkupno = $(`<td class="stavka-iznos">0</td>`);
    prototip.append(tdStavkaUkupno);

    let dugmeObrisi = $(`<button type="button" class="btn btn-danger btn-obrisi-stavku">X</button>`);
    dugmeObrisi.click(brisanjeStavke);
    td = $(`<td></td>`);
    td.append(dugmeObrisi);
    prototip.append(td);

    $('#stavke-table-body tr:last').before(prototip);
})





