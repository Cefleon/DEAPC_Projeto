	document.addEventListener('DOMContentLoaded', function() {
	    const stockForm = document.getElementById('stockForm');
	    const confirmationPopup = document.getElementById('confirmationPopup');
	    const confirmYes = document.getElementById('confirmYes');
	    const confirmNo = document.getElementById('confirmNo');

	    // Interceptar o envio do formulário
	    stockForm.addEventListener('submit', function(e) {
		e.preventDefault(); // Impede o envio padrão do formulário
		confirmationPopup.style.display = 'flex'; // Mostra a pop-up
	    });

	    // Botão "Sim" - Envia o formulário
	    confirmYes.addEventListener('click', function() {
		confirmationPopup.style.display = 'none'; // Esconde a pop-up
		stockForm.removeEventListener('submit', arguments.callee); // Remove o listener anterior
		stockForm.submit(); // Envia o formulário
	    });

	    // Botão "Não" - Fecha a pop-up
	    confirmNo.addEventListener('click', function() {
		confirmationPopup.style.display = 'none'; // Esconde a pop-up
	    });

	    // Mostrar mensagem de sucesso se houver parâmetro na URL
	    const urlParams = new URLSearchParams(window.location.search);
	    if (urlParams.has('status') && urlParams.get('status') === 'success') {
		alert('Stock atualizado com sucesso!');
	    }
	});
