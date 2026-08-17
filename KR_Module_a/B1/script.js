const $gradientBox = document.getElementById('gradientBox');
const $startInput = document.getElementById('startColor');
const $endInput = document.getElementById('endColor');

const activeColors = {
    start: $startInput.value,
    end: $endInput.value
};

const randomHexColor = () => '#' + Math.floor(Math.random() * 0x1000000).toString(16).padStart(6, '0');

function applyColor(side, value) {
    const hex = value.trim();

    if(!/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(hex)) return;

    activeColors[side] = hex;
    $gradientBox.style.background = `linear-gradient(to right, ${activeColors.start}, ${activeColors.end})`;
}

function buildPalette(container, input, side) {
    for (let i = 0; i < 12; i++) {
        const color = randomHexColor();
        const button = document.createElement('button');

        button.className = 'color-button';
        button.style.background = color;

        button.addEventListener('click', function() {
            input.value = color;
            applyColor(side, color);
        });

        container.append(button);
    }
}

buildPalette(document.getElementById('startColors'), $startInput, 'start');
buildPalette(document.getElementById('endColors'), $endInput, 'end');

$startInput.addEventListener('input', e => applyColor('start', e.target.value));
$endInput.addEventListener('input', e => applyColor('end', e.target.value));

applyColor('start', $startInput.value);
applyColor('end', $endInput.value);