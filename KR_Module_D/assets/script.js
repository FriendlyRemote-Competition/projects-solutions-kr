fetch('./media_files/content/hotel-copy.json').then(res => res.json())
.then(res => {
    const $eyebrow = document.querySelector('#hotel-story .eyebrow');
    const $body = document.querySelector('#hotel-story .body');
    const $heading = document.querySelector('#hotel-story .heading');

    $eyebrow.innerHTML = res.eyebrow;
    $body.innerHTML = res.body;
    $heading.innerHTML = res.heading;

    const $labels = document.querySelectorAll('#hotel-story .label');

    res.stats.forEach(({value, suffix, label}, idx) => {
        const $h3 = $labels[idx].querySelector('h3');
        const $span = $labels[idx].querySelector('span');

        $labels[idx].setAttribute('data-target', value);

        $h3.innerHTML = `<span class="stat-number">${value}</span> ${suffix}`;
        $span.innerHTML = label.toUpperCase();
    })
})

const statsObserver = new IntersectionObserver(entries => {
    const entry = entries[0];
    if (!entry.isIntersecting) return;

    const statItems = entry.target.querySelectorAll('.label');
    statItems.forEach(item => {
        const target = parseInt(item.dataset.target);
        const numberElement = item.querySelector('.stat-number');
        const duration = 3000;
        const startTime = performance.now();

        function updateContent(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const eased = 1 - Math.pow(1 - progress, 3);
            numberElement.textContent = Math.floor(eased * target);

            if (progress < 1) {
                requestAnimationFrame(updateContent);
            } else {
                numberElement.textContent = target;
            }
        }

        requestAnimationFrame(updateContent);
    });

    statsObserver.unobserve(entry.target);
}, {threshold: .3});

statsObserver.observe(document.getElementById('hotel-story'));

const $mobileBtn = document.querySelector('.mobile-btn');
const $mobileSlide = document.querySelector('#mobile-slide');
const $closeBtn = document.querySelector('.close-btn');
let isMobile = false;

window.addEventListener('keydown', function(e) {
    if(e.key === 'Escape' && isMobile) {
        menuToggle();
    }
});

[$mobileBtn, $closeBtn].forEach($btn => {
    $btn.addEventListener('click', function() {
        menuToggle();
    });
})

function menuToggle() {
    isMobile = !isMobile;
    $mobileBtn.setAttribute('aria-expanded', isMobile);
    if(isMobile) {
        $closeBtn.focus();
        document.body.style.overflowY = 'hidden';
    } else {
        $mobileBtn.focus();
        document.body.style.overflowY = 'auto';
    }

    $mobileSlide.classList.toggle('active',isMobile);
}

const sections = document.querySelectorAll('section');
const navLinks = document.querySelectorAll('.nav-link[data-section]');

const sectionObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;

        const sectionId = entry.target.id;
        navLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.section === sectionId);
        });
    });
}, {rootMargin: '-20% 0px -80% 0px'});

sections.forEach(section => sectionObserver.observe(section));

window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    const $hero = document.getElementById('hero');
    header.classList.toggle('scrolled', window.scrollY > ($hero.offsetHeight * .2));
});

const storageName = 'kr_storage_';

let roomIdx = JSON.parse(localStorage[storageName + 'roomIdx'] || 0);
let rooms;

const $lineArea = document.querySelector('.line-area');
fetch('./media_files/data/rooms.json').then(res => res.json())
    .then(res => {
        rooms = res;

        rooms.forEach(room => {
          const div = document.createElement('div');
          div.className = 'col d-flex flex-column room-type gap-2';
          div.innerHTML = `<div class="line"></div>
                    <span>${room.name}</span>`;
          $lineArea.append(div);
        });

        function activeRoom() {
            const $roomTypes = document.querySelectorAll('.room-type');

            $roomTypes.forEach($room => $room.classList.remove('active'));
            $roomTypes[roomIdx].classList.add('active');

            const $item = document.querySelector('.room-item');

            const room = rooms[roomIdx];

            const $roomImage = $item.querySelector('.room-image');
            $roomImage.src = `./media_files/` + room.image;
            $roomImage.alt = room.description;

            const $index = $item.querySelector('.index');
            $index.innerHTML = `${String(roomIdx + 1).padStart(2, '0')} / ${String(rooms.length).padStart(2, '0')}`;

            const $name = $item.querySelector('.name');
            $name.innerHTML = room.name;

            const $description = $item.querySelector('.description');
            $description.innerHTML = room.description;

            const $size = $item.querySelector('.size');
            $size.innerHTML = room.size;

            const $guest = $item.querySelector('.guest');
            $guest.innerHTML = room.guests;

            const $bed = $item.querySelector('.bed');
            $bed.innerHTML = room.bed;

            const $price = $item.querySelector('.price');
            $price.innerHTML = room.price.toLocaleString();

        }

        activeRoom();

        function moveRight() {
            roomIdx = Math.min(rooms.length - 1, roomIdx + 1);
            localStorage[storageName + 'roomIdx'] = JSON.stringify(roomIdx);
            activeRoom();
        }

        function moveLeft() {
            roomIdx = Math.max(0, roomIdx - 1);
            localStorage[storageName + 'roomIdx'] = JSON.stringify(roomIdx);
            activeRoom();
        }

        window.addEventListener('keydown', function(e) {
            if(e.key === 'ArrowRight') {
                moveRight();
            }
            if(e.key === 'ArrowLeft') {
                moveLeft();
            }
        });

        document.querySelector('#rooms .arrow-left').addEventListener('click', moveLeft);
        document.querySelector('#rooms .arrow-right').addEventListener('click', moveRight);
    });

fetch('./media_files/data/nearby.json').then(res => res.json())
    .then(res => {
        const $itemArea = document.querySelector('#shanghai .item-area');

        res.forEach((r,idx) => {
           $itemArea.innerHTML += `<div class="bg-white p-3 hstack gap-4 item ${r.id}" tabindex="0">
                        <img src="./media_files/${r.image}" class="col-3" style="height: 125px" alt="${r.description}">
                        <div class="vstack justify-content-center">
                            <small class="text-uppercase">${String(idx + 1).padStart(2, '0')} ${r.travelMode} ${r.duration}</small>
                            <h3>${r.name}</h3>
                            <p class="small text-muted">${r.description}</p>
                        </div>
                    </div>`
        });
    })

const yearText = new Date().getFullYear();

document.getElementById('year').innerHTML = yearText;

const $footerForm = document.querySelector('.footer-form');
const $emailInput = document.getElementById('email');

$footerForm.addEventListener('submit', e => {
    e.preventDefault();

    const value = $emailInput.value;

    $emailInput.classList.remove('is-invalid');
    $emailInput.classList.remove('is-valid');
    document.getElementById('email-feedback').innerHTML = '';
    document.getElementById('email-feedback').classList.remove('invalid-feedback');
    document.getElementById('email-feedback').classList.remove('valid-feedback');


    let errorMessage = '';
    errorMessage = `Thank you. Shanghai stories are on their way.`;

    if(!value.trim()) errorMessage = `Please enter your email address.`;

    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) errorMessage = `Please enter a valid email address.`;

    if(errorMessage !== 'Thank you. Shanghai stories are on their way.') {
        $emailInput.classList.add('is-invalid');
        document.getElementById('email-feedback').classList.add('invalid-feedback');
        document.getElementById('email-feedback').innerHTML = errorMessage;
    } else {
        $emailInput.classList.add('is-valid');
        document.getElementById('email-feedback').classList.add('valid-feedback');
        document.getElementById('email-feedback').innerHTML = errorMessage;

    }

})