const teraz = new Date();
const siatka = document.getElementById('siatka');
const tytul = document.getElementById('tytul');

// aktualnie wyświetlany miesiąc (zawsze 1. dzień, żeby uniknąć błędów przy 31. itp.)
let widok = new Date(teraz.getFullYear(), teraz.getMonth(), 1);

function dodajKafelek(numer, klasy = []) {
    const el = document.createElement('div');
    el.className = ('dzien ' + klasy.join(' ')).trim();
    el.textContent = numer;
    siatka.appendChild(el);
}

function rysujKalendarz() {
    const rok = widok.getFullYear();
    const miesiac = widok.getMonth();

    siatka.innerHTML = '';
    tytul.textContent = widok.toLocaleDateString('pl-PL', { month: 'long', year: 'numeric' });

    // nagłówki dni tygodnia
    ['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So', 'Nd'].forEach(nazwa => {
        const el = document.createElement('div');
        el.className = 'nazwa-dnia';
        el.textContent = nazwa;
        siatka.appendChild(el);
    });

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
        dodajKafelek(d, klasy);
    }

    // początek następnego miesiąca (dopełnienie do 6 wierszy = 42 kafelki)
    const reszta = (7 - ((przesuniecie + liczbaDni) % 7)) % 7;
    for (let d = 1; d <= reszta; d++) {
        dodajKafelek(d, ['inny']);
    }
}

document.getElementById('poprzedni').addEventListener('click', () => {
    widok.setMonth(widok.getMonth() - 1);
    rysujKalendarz();
});

document.getElementById('nastepny').addEventListener('click', () => {
    widok.setMonth(widok.getMonth() + 1);
    rysujKalendarz();
});

rysujKalendarz();