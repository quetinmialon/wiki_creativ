document.addEventListener('DOMContentLoaded', () => {
    window.copyPassword = function (id) {
        const passwordSpan = document.getElementById(`password-${id}`);
        const password = passwordSpan.textContent;

        navigator.clipboard.writeText(password)
            .then(() => {
                alert("Mot de passe copié dans le presse-papiers !");
            })
            .catch(err => {
                console.error("Erreur lors de la copie :", err);
                alert("Échec de la copie du mot de passe.");
            });
    };

    window.copySharedPassword = function(id){
        const sharedPasswordSpan = document.getElementById(`password-${id}`);
        const sharedPassword = sharedPasswordSpan.textContent;

        navigator.clipboard.writeText(sharedPassword)
            .then(() => {
                alert("Mot de passe copié dans le presse-papiers !");
            })
            .catch(err => {
                console.error("Erreur lors de la copie :", err);
                alert("Échec de la copie du mot de passe.");
            });
    }
});
