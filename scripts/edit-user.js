document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('editUserId').value = btn.dataset.id;
      document.getElementById('editUserEmail').value = btn.dataset.email;
      document.getElementById('editUsername').value = btn.dataset.username;
      document.getElementById('editUserRole').value = btn.dataset.role;
    });
  });
});