document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('stockForm');
    const popup = document.getElementById('confirmationPopup');
    const yesBtn = document.getElementById('confirmYes');
    const noBtn = document.getElementById('confirmNo');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        popup.style.display = 'flex';
    });

    yesBtn.addEventListener('click', function () {
        popup.style.display = 'none';
        form.submit();
    });

    noBtn.addEventListener('click', function () {
        popup.style.display = 'none';
    });

    // Mostrar mensagem de sucesso se houver parâmetro na URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status') && urlParams.get('status') === 'success') {
        alert('Stock atualizado com sucesso!');
    }
});
