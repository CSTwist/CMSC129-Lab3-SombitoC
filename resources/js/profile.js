// resources/js/profile.js

document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editProfileBtn');

    if (!editBtn) return;

    const emailInput = document.getElementById('emailInput');
    const actionButtons = document.getElementById('actionButtons');
    const cancelBtn = document.getElementById('cancelBtn');
    const originalEmail = emailInput.value;

    // if "Edit" is clicked
    editBtn.addEventListener('click', function() {
        emailInput.disabled = false;
        emailInput.focus();
        editBtn.style.display = 'none';
        actionButtons.style.setProperty('display', 'flex', 'important');
    });

    // if "Cancel" is clicked
    cancelBtn.addEventListener('click', function() {
        emailInput.disabled = true;
        emailInput.value = originalEmail;
        editBtn.style.display = 'block';
        actionButtons.style.setProperty('display', 'none', 'important');
    });
});
