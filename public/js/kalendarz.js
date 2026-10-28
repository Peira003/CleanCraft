const DNI = ['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So', 'Nd'];

const teraz = new Date();
const siatka = document.getElementById('siatka');
const tytul = document.getElementById('tytul');

let widok = new Date(teraz.getFullYear(), teraz.getMonth(), 1);

const pad = n => String(n).padStart(2, '0');
const format = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
const czlonek = id => PLAN.members.find(m => m.id === id);

function el(klasa, tekst = '') {
    const e = document.createElement('div');
    e.className = klasa;
    e.textContent = tekst;
    return e;
}

function rysujKalendarz() {
    const rok = widok.getFullYear();
    const miesiac = widok.getMonth();

    siatka.innerHTML = '';
    tytul.textContent = widok.toLocaleDateString('pl-PL', { month: 'long', year: 'numeric' });

    const naglowek = el('nazwy-dni');
    DNI.forEach(n => naglowek.appendChild(el('nazwa-dnia', n)));
    siatka.appendChild(naglowek);

    const przesuniecie = (new Date(rok, miesiac, 1).getDay() + 6) % 7;
    const liczbaDni = new Date(rok, miesiac + 1, 0).getDate();
    const wiersze = Math.ceil((przesuniecie + liczbaDni) / 7);
    const dzisiaj = format(teraz);

    for (let w = 0; w < wiersze; w++) {
        const poniedzialek = new Date(rok, miesiac, 1 - przesuniecie + (w * 7));
        const klucz = format(poniedzialek);
        const tydzien = el('tydzien');

        const dyzurny = czlonek(PLAN.schedule[klucz]);
        if (dyzurny) {
            tydzien.style.borderColor = dyzurny.color;
            tydzien.style.background = dyzurny.color + '24'; // ~14% krycia
            tydzien.title = 'Sprząta: ' + dyzurny.name;
        }

        if (PLAN.isOwner) {
            tydzien.classList.add('edytowalny');
            tydzien.addEventListener('click', () => otworzWybor(klucz, poniedzialek));
        }

        for (let i = 0; i < 7; i++) {
            const dzien = new Date(rok, miesiac, 1 - przesuniecie + (w * 7) + i);
            const klasy = ['dzien'];
            if (dzien.getMonth() !== miesiac) klasy.push('inny');
            if (dzien.getDay() === 0 || dzien.getDay() === 6) klasy.push('weekend');
            if (format(dzien) === dzisiaj) klasy.push('dzisiaj');
            tydzien.appendChild(el(klasy.join(' '), dzien.getDate()));
        }

        siatka.appendChild(tydzien);
    }
}

/* ---------- wybór dyżurnego (tylko właściciel) ---------- */
const weekModal = document.getElementById('weekModal');

function otworzWybor(klucz, poniedzialek) {
    if (!weekModal) return;

    const niedziela = new Date(poniedzialek.getFullYear(), poniedzialek.getMonth(), poniedzialek.getDate() + 6);
    const opcje = { day: 'numeric', month: 'long' };
    document.getElementById('weekLabel').textContent =
        poniedzialek.toLocaleDateString('pl-PL', opcje) + ' – ' + niedziela.toLocaleDateString('pl-PL', opcje);
    document.getElementById('weekError').hidden = true;

    const picker = document.getElementById('picker');
    picker.innerHTML = '';

    PLAN.members.forEach(m => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'picker-btn';

        const kropka = document.createElement('span');
        kropka.className = 'swatch';
        kropka.style.background = m.color;

        btn.append(kropka, m.name); // nazwa jako tekst, nie HTML
        btn.addEventListener('click', () => zapisz(klucz, m.id));
        picker.appendChild(btn);
    });

    const nikt = document.createElement('button');
    nikt.type = 'button';
    nikt.className = 'picker-btn';
    nikt.textContent = 'Nikt (wyczyść tydzień)';
    nikt.addEventListener('click', () => zapisz(klucz, 0));
    picker.appendChild(nikt);

    weekModal.showModal();
}

async function zapisz(klucz, userId) {
    const dane = new FormData();
    dane.append('csrf', PLAN.csrf);
    dane.append('week', klucz);
    dane.append('user_id', userId);

    try {
        const res = await fetch(PLAN.url, { method: 'POST', body: dane });
        if (!res.ok) throw new Error();

        if (userId) PLAN.schedule[klucz] = userId;
        else delete PLAN.schedule[klucz];

        if (weekModal) weekModal.close();
        rysujKalendarz();
    } catch {
        document.getElementById('weekError').hidden = false;
    }
}

if (weekModal) {
    document.getElementById('weekCancel').addEventListener('click', () => weekModal.close());
    weekModal.addEventListener('click', e => { if (e.target === weekModal) weekModal.close(); });
}

function zmienMiesiac(delta) {
    widok.setMonth(widok.getMonth() + delta);
    rysujKalendarz();
}

document.getElementById('poprzedni').addEventListener('click', () => zmienMiesiac(-1));
document.getElementById('nastepny').addEventListener('click', () => zmienMiesiac(1));

rysujKalendarz();