/** @type {HTMLDivElement} */
const error = document.querySelector('[data-target="api-token-error"]');
/** @type {HTMLParagraphElement} */
const container = document.querySelector('[data-target="api-token-container"]');
/** @type {HTMLButtonElement} */
const button = document.querySelector('[data-target="api-token-button"]');

button.addEventListener("click", function (event) {
    fetch(button.dataset.url, {
        method: button.dataset.method
    }).then(res => {
        return res.json();
    }).then(body => {
        if ('error' in body) {
            error.innerText = body.error;
            error.classList.remove('d-none');
            return;
        }

        if ('token' in body) {
            error.classList.add('d-none');
            const token = body.token;
            const text = container.innerText;
            const pos = text.indexOf(":");
            container.innerText = text.slice(0, pos + 1) + " " + token;
        }
    });
});
