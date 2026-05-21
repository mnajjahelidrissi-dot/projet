import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import api from '../../../services/api';

const ForgotPassword = () => {
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            await api.post('/forgot-password', { email });
            setSuccess(true);
        } catch (err) {
            setError(err.response?.data?.message || 'Email introuvable');
        } finally {
            setLoading(false);
        }
    };

    if (success) {
        return (
            <div className="login-container">
                <div className="login-card">
                    <div className="login-header">
                        <h1>Email envoyé !</h1>
                        <p>Un lien de réinitialisation a été envoyé à <strong>{email}</strong></p>
                    </div>
                    <Link to="/login" className="login-button" style={{ textAlign: 'center', display: 'block', textDecoration: 'none' }}>
                        Retour à la connexion
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="login-container">
            <div className="login-card">
                <div className="login-header">
                    <h1>Mot de passe oublié</h1>
                    <p>Entrez votre email pour recevoir un lien de réinitialisation</p>
                </div>

                {error && <div className="error-message">{error}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="input-group">
                        <label>Email</label>
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="votre@email.com"
                            required
                        />
                    </div>

                    <button type="submit" className="login-button" disabled={loading}>
                        {loading ? 'Envoi en cours...' : 'Envoyer le lien'}
                    </button>

                    <div className="login-footer">
                        <Link to="/login" className="forgot-link">← Retour à la connexion</Link>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default ForgotPassword;
