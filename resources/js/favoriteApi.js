
document.addEventListener('DOMContentLoaded', () => {
    window.toggleFavorite = async function (documentId, userId) {
        fetch(`/api/favorites/${documentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },

            body: {
                userId: userId
            }

        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };
});

