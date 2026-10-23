/* Progressive enhancement only: purchase and spam checks also run on the server. */
(function () {
    'use strict';
    const form = document.querySelector('.support-form__fields');
    if (!form) return;
    const kind = form.querySelector('#support-kind');
    const order = form.querySelector('#support-order');
    const issue = form.querySelector('#support-issue');
    const submit = form.querySelector('[type="submit"]');
    if (!kind || !order || !issue || !submit) return;
    const rows = Array.from(form.querySelectorAll('.support-form__row')).filter(function (row) {
        return !row.hasAttribute('data-support-route') && !row.hasAttribute('data-support-help');
    });
    function updateRoute() {
        const acceptsMessage = kind.value === 'aftersales' || kind.value === 'presales';
        rows.forEach(function (row) {
            const hide = !acceptsMessage || (row.contains(order) && kind.value === 'presales');
            row.hidden = hide;
            row.querySelectorAll('input, select, textarea').forEach(function (field) { field.disabled = hide; });
        });
        form.querySelectorAll('[data-support-help]').forEach(function (help) {
            help.hidden = help.dataset.supportHelp !== kind.value && !(kind.value === 'aftersales' && help.dataset.supportHelp === 'order-help');
        });
        const orderHelp = form.querySelector('[data-support-help="order-help"] details');
        if (kind.value === 'order-help' && orderHelp) orderHelp.open = true;
        form.querySelectorAll('[data-support-kind]').forEach(function (option) {
            option.disabled = option.dataset.supportKind !== kind.value;
            option.hidden = option.disabled;
        });
        if (issue.selectedOptions[0] && issue.selectedOptions[0].disabled) issue.value = '';
        order.required = kind.value === 'aftersales';
        order.setAttribute('aria-required', String(order.required));
        submit.hidden = !acceptsMessage;
        submit.disabled = !acceptsMessage;
        form.querySelector('.support-form__required-note').hidden = !acceptsMessage;
    }
    kind.addEventListener('change', updateRoute);
    form.addEventListener('submit', function (event) {
        if (!form.reportValidity()) {
            event.preventDefault();
            return;
        }
        // Native form navigation only; never introduce a second AJAX send.
        submit.disabled = true;
    });
    window.addEventListener('pageshow', function () { updateRoute(); });
    updateRoute();
}());
