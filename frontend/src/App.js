import React, { useState, useEffect } from 'react';
import './App.css';

function App() {
  const [messages, setMessages] = useState([]);
  const [content, setContent] = useState('');
  const [error, setError] = useState(null);

  // Fonction pour charger les messages
  const fetchMessages = () => {
    fetch('http://localhost:8000/api/chat/messages')
      .then(res => res.json())
      .then(data => setMessages(data))
      .catch(err => {
        console.error('Erreur chargement messages:', err);
        setError('Impossible de charger les messages');
      });
  };

  useEffect(() => {
    // Charger les messages au démarrage
    fetchMessages();

    // Mettre en place le polling toutes les 3 secondes
    const intervalId = setInterval(fetchMessages, 3000);

    // Nettoyer à la désactivation du composant
    return () => clearInterval(intervalId);
  }, []);

  const sendMessage = async (e) => {
    e.preventDefault();
    setError(null);

    try {
      const response = await fetch('http://localhost:8000/api/chat/send', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ content }),
      });

      console.log('Status de la réponse:', response.status);
      const text = await response.text();
      console.log('Réponse brute du serveur:', text);

      try {
        const data = JSON.parse(text);
        console.log('Données JSON reçues:', data);

        if (response.ok) {
          console.log('Message envoyé avec succès, on vide le champ');
          setContent('');
        } else {
          setError(data.message || 'Erreur lors de l’envoi du message');
          console.error('Erreur serveur:', data);
        }
      } catch (parseError) {
        console.error('Erreur de parsing JSON:', parseError);
        console.error('Réponse serveur non JSON:', text);
      }
    } catch (err) {
      setError('Erreur réseau lors de l’envoi');
      console.error('Erreur fetch:', err);
    }
  };

  return (
    <div className="App">
      <h1>Messenger</h1>

      {error && <div style={{ color: 'red' }}>{error}</div>}

      <div className="messages" style={{ maxHeight: '300px', overflowY: 'auto' }}>
        {messages.map((msg, index) => (
          <p key={index}>
            <strong>{msg.user} :</strong> {msg.content} <em>{msg.date}</em>
          </p>
        ))}
      </div>

      <form onSubmit={sendMessage}>
        <input
          type="text"
          value={content}
          onChange={e => setContent(e.target.value)}
          placeholder="Tape ton message"
          required
        />
        <button type="submit">Envoyer</button>
      </form>
    </div>
  );
}

export default App;
