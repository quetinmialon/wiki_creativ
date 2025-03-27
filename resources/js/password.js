document.addEventListener('DOMContentLoaded', () => {
    window.togglePassword = function (id) {
        let passwordSpan = document.getElementById(`password-${id}`);
        if (passwordSpan.classList.contains('hidden')) {
            passwordSpan.classList.remove('hidden');
        } else {
            passwordSpan.classList.add('hidden');
        }
    }

    window.toggleSharedPassword = function(id){
        let sharedPasswordSpan = document.getElementById(`shared-password-${id}`);
        if (sharedPasswordSpan.classList.contains('hidden')) {
            sharedPasswordSpan.classList.remove('hidden');
        } else {
            sharedPasswordSpan.classList.add('hidden');
        }
    }
});
