const DNI = ['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So', 'Nd'];

const teraz = new Date();
const siatka = document.getElementById('siatka');
const tytul = document.getElementById('tytul');

// aktualnie wyświetlany miesiąc (zawsze 1. dzień, żeby uniknąć błędów przy 31. itp.)
let widok = new Date(teraz.getFullYear(), teraz.getMonth(), 1);

function el(klasa, tekst) {
    const e = document.createElement('div');
    e.className = klasa;
    e.textContent = tekst;
    return e;
}

function format(rok, miesiac, dzien) {
    return `${rok}-${String(miesiac + 1).padStart(2, '0')}-${String(dzien).padStart(2, '0')}`;
}

function dodajKafelek(numer, klasy = [], data = null) {
    const kafelek = el(('dzien ' + klasy.join(' ')).trim(), numer);
    if (data) kafelek.dataset.data = data;
    siatka.appendChild(kafelek);
}

function rysujKalendarz() {
    const rok = widok.getFullYear();
    const miesiac = widok.getMonth();

    siatka.innerHTML = '';
    tytul.textContent = widok.toLocaleDateString('pl-PL', { month: 'long', year: 'numeric' });

    // nagłówki dni tygodnia
    DNI.forEach(nazwa => siatka.appendChild(el('nazwa-dnia', nazwa)));

    const przesuniecie = (new Date(rok, miesiac, 1).getDay() + 6) % 7;
    const liczbaDni = new Date(rok, miesiac + 1, 0).getDate();
    const dniPoprzedniego = new Date(rok, miesiac, 0).getDate();

    // koniec poprzedniego miesiąca
    for (let i = 0; i < przesuniecie; i++) {
        dodajKafelek(dniPoprzedniego - przesuniecie + 1 + i, ['inny']);
    }

    // bieżący miesiąc
    const jestTenMiesiac = rok === teraz.getFullYear() && miesiac === teraz.getMonth();
    for (let d = 1; d <= liczbaDni; d++) {
        const klasy = [];
        const dt = new Date(rok, miesiac, d).getDay();
        if (dt === 0 || dt === 6) klasy.push('weekend');
        if (jestTenMiesiac && d === teraz.getDate()) klasy.push('dzisiaj');
        dodajKafelek(d, klasy, format(rok, miesiac, d));
    }

    // początek następnego miesiąca (dopełnienie do 6 wierszy = 42 kafelki)
    const reszta = (7 - ((przesuniecie + liczbaDni) % 7)) % 7;
    for (let d = 1; d <= reszta; d++) {
        dodajKafelek(d, ['inny']);
    }
}

function zmienMiesiac(delta) {
    widok.setMonth(widok.getMonth() + delta);
    rysujKalendarz();
}

document.getElementById('poprzedni').addEventListener('click', () => zmienMiesiac(-1));
document.getElementById('nastepny').addEventListener('click', () => zmienMiesiac(1));

rysujKalendarz();