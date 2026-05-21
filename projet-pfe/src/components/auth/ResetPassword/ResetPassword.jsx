import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, useLocation } from 'react-router-dom';
import api from '../../../services/api';




const ResetPassword = () => {
    const { token } = useParams();
    const location = useLocation();
    const navigate = useNavigate();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState(false);

    useEffect(() => {
        const params = new URLSearchParams(location.search);
        const emailParam = params.get('email');
        if (emailParam) setEmail(emailParam);
    }, [location]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');

        if (password !== passwordConfirmation) {
            setError('Les mots de passe ne correspondent pas');
            return;
        }

        if (password.length < 8) {
            setError('Le mot de passe doit contenir au moins 8 caractères');
            return;
        }

        setLoading(true);
        try {
            await api.post('/reset-password', {
                email,
                token,
                password,
                password_confirmation: passwordConfirmation
            });
            setSuccess(true);
        } catch (err) {
            setError(err.response?.data?.message || 'Lien invalide ou expiré');
        } finally {
            setLoading(false);
        }
    };

    if (success) {
        return (
            <div className="login-container">
                <div className="login-card">
                    <div className="login-header">
                        <h1>Mot de passe modifié !</h1>
                        <p>Votre mot de passe a été réinitialisé avec succès.</p>
                    </div>
                    <button onClick={() => navigate('/login')} className="login-button">
                        Se connecter
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="login-container">
            <div className="login-card">
                <div className="login-header">
                    <h1>Nouveau mot de passe</h1>
                    <p>Choisissez un nouveau mot de passe pour votre compte</p>
                </div>

                {error && <div className="error-message">{error}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="input-group">
                        <label>Email</label>
                        <input type="email" value={email} disabled required />
                    </div>

                    <div className="input-group">
                        <label>Nouveau mot de passe</label>
                        <input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="8 caractères minimum"
                            required
                        />
                    </div>

                    <div className="input-group">
                        <label>Confirmer le mot de passe</label>
                        <input
                            type="password"
                            value={passwordConfirmation}
                            onChange={(e) => setPasswordConfirmation(e.target.value)}
                            placeholder="retapez votre mot de passe"
                            required
                        />
                    </div>

                    <button type="submit" className="login-button" disabled={loading}>
                        {loading ? 'Changement en cours...' : 'Réinitialiser'}
                    </button>

                    <div className="login-footer">
                        <a href="/login" className="forgot-link">← Retour à la connexion</a>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default ResetPassword;
