/** @type {HTMLInputElement} */
const imageInput = document.querySelector('input.custom-file-input');
const imageLabel = document.querySelector('label.custom-file-label');

imageInput.addEventListener('change', event => {
    imageLabel.innerText = Array.from(event.target.files).map(file => file.name).join(', ');
});

const template = `<div class="row" id="create_article_form_promotedWords___name__">
                <div class="col">
                    <div class="form-label-group">
                        <input  type="text"
                                id="create_article_form_promotedWords___name___word"
                                name="create_article_form[promotedWords][__name__][word]"
                                placeholder="Продвигаемое слово"
                                class="form-control"
                        >
                        <label for="create_article_form_promotedWords___name___word">Продвигаемое слово</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-label-group">
                        <input  type="text"
                                id="create_article_form_promotedWords___name___repetitions"
                                name="create_article_form[promotedWords][__name__][repetitions]"
                                placeholder="кол-во"
                                class="form-control"
                        >
                        <label for="create_article_form_promotedWords___name___repetitions">кол-во</label>
                    </div>
                </div>
            </div>`;

function createPromotedWordInput(index) {
    return template.replaceAll('__name__', index);
}

function getLastInput() {
    const inputs = document.querySelectorAll('input[id^="create_article_form_promotedWords_"][id$="_word"]');
    return inputs[inputs.length - 1];
}

function getInputsAmount() {
    return document.querySelectorAll('input[id^="create_article_form_promotedWords_"][id$="_word"]').length;
}

function addInput(e) {
    if (e.target !== getLastInput()) {
        return;
    }
    const n = getInputsAmount();
    const html = createPromotedWordInput(n);

    const parent = e.target.closest('div.card-body');
    parent.insertAdjacentHTML('beforeend', html);

    const input = document.querySelector(`input[id="create_article_form_promotedWords_${n}_word"]`);
    input.addEventListener("input", addInput)
    input.addEventListener("input", removeLastInput);
}

function removeLastInput(e) {
    if (e.target.value === '' && getInputsAmount() !== 1) {
        const input = getLastInput();
        if (input.value !== '') {
            return;
        }

        const parent = input.closest('div.row');

        const repetitionsInput = parent.querySelector('input[id^="create_article_form_promotedWords_"][id$="_repetitions"]');

        if (!Number.isNaN(repetitionsInput.valueAsNumber) && repetitionsInput.valueAsNumber !== 0) {
            return;
        }

        parent.remove();
    }
}
/** @type {HTMLInputElement} */
const input = getLastInput();

input.addEventListener("input", addInput);
input.addEventListener("input", removeLastInput);
