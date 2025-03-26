document.addEventListener('DOMContentLoaded', () => {
    window.toggleFavorite = async function (documentId, userId) {
        const button = document.getElementById(`favorite-btn-${documentId}`);

        fetch(`/api/favorites/${documentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ userId: userId })
        })
        .then(response => response.json())
        .then(data => {
            button.textContent = data.favorited ? 'Retirer des favoris' : 'Ajouter aux favoris';
            button.classList.toggle('hover:bg-red-700', data.favorited);
            button.classList.toggle('bg-red-500', data.favorited);
            button.classList.toggle('hover:bg-blue-700',!data.favorited);
            button.classList.toggle('bg-blue-500', !data.favorited);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };
});
