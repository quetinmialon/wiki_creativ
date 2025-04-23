
document.addEventListener('DOMContentLoaded', function () {
    const publicCheckbox = document.getElementById('public');
    const categoriesDiv = document.getElementById('categoriesDiv');

    const toggleCategoriesVisibility = () => {
        if (publicCheckbox.checked) {
            categoriesDiv.style.display = 'none';
        } else {
            categoriesDiv.style.display = 'block';
        }
    };

    // Initial state
    toggleCategoriesVisibility();

    // Listener for change
    publicCheckbox.addEventListener('change', toggleCategoriesVisibility);
});

