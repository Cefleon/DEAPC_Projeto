document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('.products-table tbody');

    function addRowIfNeeded(e) {
        const rows = tbody.querySelectorAll('tr');
        const lastRow = rows[rows.length - 1];
        if (e.target === lastRow.querySelector('input[name^="product"]')) {
            const newRow = lastRow.cloneNode(true);
            const newIndex = rows.length + 1;

            newRow.querySelectorAll('input, select').forEach(function (el) {
                if (el.name.startsWith('product')) el.value = '';
                if (el.name.startsWith('date')) el.value = '';
                if (el.name.startsWith('quantity')) el.value = '';
                if (el.name.startsWith('type')) el.selectedIndex = 0;

                el.name = el.name.replace(/\d+$/, newIndex);
                el.required = false;
            });

            tbody.appendChild(newRow);

            newRow.querySelector('input[name^="product"]').addEventListener('focus', addRowIfNeeded);
        }
    }

    tbody.querySelectorAll('input[name^="product"]').forEach(function (input) {
        input.addEventListener('focus', addRowIfNeeded);
    });
});