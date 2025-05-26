document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.comment-form');

    forms.forEach(form => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const articleId = form.dataset.articleId;
            const actionUrl = form.getAttribute('action');
            const formData = new FormData(form);

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Erreur réseau');

                const html = await response.text();

                // Parser la réponse HTML reçue
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Trouver le nouveau bloc commentaires
                const newCommentBlock = doc.querySelector(`#comment-block-${articleId}`);
                const currentCommentBlock = document.querySelector(`#comment-block-${articleId}`);

                if (newCommentBlock && currentCommentBlock) {
                    // Remplacer le contenu actuel par le nouveau (avec le nouveau commentaire)
                    currentCommentBlock.innerHTML = newCommentBlock.innerHTML;
                } else {
                    alert("Impossible de mettre à jour les commentaires.");
                }

            } catch (error) {
                console.error('Erreur lors de l\'envoi du commentaire:', error);
                alert("Échec de l'envoi du commentaire.");
            }
        });
    });
});
