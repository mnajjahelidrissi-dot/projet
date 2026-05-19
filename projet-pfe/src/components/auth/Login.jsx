//ce composant va afficher le formulaire de connexion
//Utiliser le contexte d'authentification pour se connecter
import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'

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

        <div>
            <div>
                <h1>SAHAM Bank</h1>
                <p>Connectez-vous à votre espace</p>
            </div>

            {error && (
                <div>
                    {error}
                </div>
            )}

            <form onSubmit={handleSubmit}>
                <div>
                    <label>Email</label>
                    <input
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="votre@email.com"
                        required
                    />
                </div>

                <div>
                    <label>Mot de passe</label>
                    <input
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder="••••••••"
                        required
                    />
                </div>

                <button type="submit" disabled={loading}>
                    {loading ? 'Connexion...' : 'Se connecter'}
                </button>

                <div>
                    <a href="/forgot-password">Mot de passe oublié ?</a>
                </div>
            </form>
        </div>
    )
}
export default Login
