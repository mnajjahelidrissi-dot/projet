//ce composant va afficher le formulaire de connexion
//Utiliser le contexte d'authentification pour se connecter
import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'
import './Login.css'
import sahamLogo from '../../assets/saham-bank-logo.svg'

const Login = () => {
    const navigate = useNavigate()        // Pour rediriger
    //pour importer la fonction de connexion
    const { login } = useAuth()


    //Champs du formulaire vides par defaut
    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const [error, setError] = useState('')
    const [loading, setLoading] = useState(false)
    //Soumission du formulaire de connexion
    const handleSubmit = async (e) => {
        e.preventDefault()
        setError('')
        setLoading(true)

        // Appelle la fonction login du contexte
        const result = await login(email, password)

        if (result.success) {
            // Redirige vers le dashboard
            navigate('/dashboard')
        } else {
            // Affiche l'erreur
            setError(result.message)
        }

        setLoading(false)
    }
    return (

        <div className="login-container">        {/* ← AJOUTER */}
            <div className="login-card">          {/* ← AJOUTER */}
                <div className="login-header">
                    <img src={sahamLogo} alt="Logo SAHAM Bank" className="login-logo" />
                    <p>Connectez-vous à votre espace</p>
                </div>

                {error && (
                    <div className="error-message">
                        <span className="error-icon">⚠</span>
                        <p className="error-text">{error}</p>
                    </div>
                )}

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

                    <div className="input-group">
                        <label>Mot de passe</label>
                        <input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="••••••••"
                            required
                        />
                    </div>

                    <button type="submit" disabled={loading} className="login-button">
                        {loading ? 'Connexion...' : 'Se connecter'}
                    </button>

                    <div className="login-footer">
                        <a href="/forgot-password">Mot de passe oublié ?</a>
                    </div>
                </form>
            </div>
        </div>
    )
}
export default Login
