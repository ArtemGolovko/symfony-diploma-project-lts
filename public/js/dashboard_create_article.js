/** @type {HTMLInputElement} */
const imageInput = document.querySelector('input.custom-file-input');
const imageLabel = document.querySelector('label.custom-file-label');

imageInput.addEventListener('change', event => {
    imageLabel.innerText = Array.from(event.target.files).map(file => file.name).join(', ');
});

/** @type {HTMLDivElement} */
const prototypeElement = document.querySelector('[data-target="prototype"]');
const prototypeDataset = prototypeElement.dataset

const template = `<div class="row">
    <div class="col">
        ${prototypeDataset.prototypeWord}
    </div>
    <div class="col">
        ${prototypeDataset.prototypeRepetitions}
    </div>
</div>`;

function createPromotedWordInput(index) {
    return template.replaceAll('__name__', index);
}

function getLastInput() {
    return document.querySelector(`input[data-index="${prototypeDataset.prototypeIndex}"]`);
}


function addInput(e) {
    if (e.target.dataset.index !== prototypeDataset.prototypeIndex) {
        return;
    }

    prototypeDataset.prototypeIndex++;
    const html = createPromotedWordInput(prototypeDataset.prototypeIndex);

    const parent = e.target.closest('div.card-body');
    parent.insertAdjacentHTML('beforeend', html);

    const input = document.querySelector(`input[data-index="${prototypeDataset.prototypeIndex}"]`);
    input.addEventListener("input", addInput)
    input.addEventListener("input", removeLastInput);
}

function removeLastInput(e) {
    if (e.target.value !== '' || e.target.dataset.index === 0) {
        return;
    }

    const input = getLastInput();
    if (input.value !== '') {
        return;
    }

    const parent = input.closest('div.row');

    const repetitionsInput = parent.querySelector('input[id$="_repetitions"]');

    if (!Number.isNaN(repetitionsInput.valueAsNumber) && repetitionsInput.valueAsNumber !== 0) {
        return;
    }

    prototypeDataset.prototypeIndex--;
    parent.remove();
}
/** @type {HTMLInputElement} */
const input = getLastInput();

input.addEventListener("input", addInput);
input.addEventListener("input", removeLastInput);
