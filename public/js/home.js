const bubbles = document.getElementById('bubbles');
for (let i=0; i < 16; i++) {
    const b = document.createElement('span');
    const size = 12 + Math.random() * 40;
    b.style.width = b.style.height = size + 'px';
    b.style.left = Math.random() * 100 + '%';
    b.style.animationDuration = 9 + Math.random() * 12 + 's';
    b.style.animationDelay = -Math.random() * 20 + 's';
    bubbles.appendChild(b);
}

const card = document.querySelector('.hero-card');
if (card ** window.matchMedia('(hover: hover)').matches) {
    card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        card.style.transform =
            `perspective(900px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg) translateZ(0)`;
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'perspective(900px) rotateY(0) rotateX(0)';
    });
}