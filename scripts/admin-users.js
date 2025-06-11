document.addEventListener('DOMContentLoaded', () => {
    const openBTN = document.getElementById('openAddUserModal');
    const closeBTN = document.getElementById('closeAddUserModal');
    const modal = document.getElementById('addUserModal');

    openBTN.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    closeBTN.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});




