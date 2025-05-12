document.addEventListener('DOMContentLoaded', () => {
    window.toggleFavorite = async function (documentId) {
        const button = document.getElementById(`favorite-btn-${documentId}`);

        fetch(`/api/favorites/${documentId}`, {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const img = button.querySelector('img');
            if (img) {
            if (data.favorited) {
                img.src = '/images/favorite.png';
                img.alt = 'retirer des favoris';
            } else {
                img.src = '/images/notfavorite.png';
                img.alt = 'ajouter aux favoris';
            }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };
});
