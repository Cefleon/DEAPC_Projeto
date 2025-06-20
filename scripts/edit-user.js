document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('button[data-bs-target="#editUserModal"]').forEach(function (button) {
        button.addEventListener('click', function () {
            // Obter dados dos atributos data-*
            const userId = button.getAttribute('data-id');
            const username = button.getAttribute('data-username');
            const email = button.getAttribute('data-email');
            const role = button.getAttribute('data-role');

            // Preencher os campos do modal
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUsername').value = username;
            document.getElementById('editUserEmail').value = email;
            document.getElementById('editUserRole').value = role;
        });
    });
});