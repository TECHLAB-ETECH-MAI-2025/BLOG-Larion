document.querySelectorAll('.like-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Empêche le rechargement

            const url = this.dataset.url;
            const token = this.dataset.token;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ _token: token })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const likeCountSpan = this.querySelector('.like-count');
                    likeCountSpan.textContent = data.likes;
                } else {
                    alert(data.message || 'Erreur');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
            });
        });
    });