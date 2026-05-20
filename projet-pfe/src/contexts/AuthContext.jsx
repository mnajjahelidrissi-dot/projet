/*ce fichier va gerer l'authentification de l'utilisateur et stocker les informations de l'utilisateur dans le contexte
Pour etre utilise dans toute l'application*/
import React, { createContext, useState, useContext, useEffect } from 'react'
import api from '../services/api'

//Creaction du contexte d'authentification qui va contenir toutes les informations de l'utilisateur et les fonctions d'authentification
//Pour les reutiliser dans d'aures composants de l'application
const AuthContext = createContext()

//Hook Personnalisé pour acceder au contexte d'authentification
export const useAuth = () => useContext(AuthContext)

//Creation du provider qui donner les infos
export const AuthProvider = ({ children }) => {

    const [user, setUser] = useState(null)        // Stocke les infos de l'utilisateur
    const [loading, setLoading] = useState(true)  // Indique si on est en train de charger

    //Verifier si l'utilisateur est deja authentifié en recuperant les infos de l'utilisateur depuis le backend
    useEffect(() => {
        checkAuth()
    }, [])
    //Fonction pour verifier si l'utilisateur est deja authentifié en recuperant les infos de l'utilisateur depuis le backend
    const checkAuth = async () => {
        //recuperer le token d'authentification et les infos de l'utilisateur depuis le localStorage
        const token = localStorage.getItem('auth_token')
        const userData = localStorage.getItem('user')
        if (token) {
            try {
                const response = await api.get('/user');
                setUser(response.data.user);
            } catch (error) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
            }
        }
        setLoading(false)
    }

    //fonction pour se connecter en envoyant les infos de connexion au backend
    // en stockant le token d'authentification et les infos de l'utilisateur dans le localStorage
    const login = async (email, password) => {
        try {
            // Appel a l'API Laravel
            const response = await api.post('/login', { email, password })

            // Si la connexion reussit
            if (response.data.success) {
                // Stocke le token dans localStorage
                localStorage.setItem('auth_token', response.data.token)
                localStorage.setItem('user', JSON.stringify(response.data.user))

                // Met a jour l'etat user
                setUser(response.data.user)

                return { success: true }
            }

            return { success: false, message: response.data.message }

        } catch (error) {
            // En cas d'erreur réseau ou serveur
            const message = error.response?.data?.message || 'Erreur de connexion'
            return { success: false, message }
        }
    }
    // fonction pour se déconnecter
    const logout = async () => {
        try {
            // Appel a l'API Laravel pour déconnecter
            await api.post('/logout')
        } catch (error) {
            console.error('Erreur déconnexion:', error)
        } finally {

            // Supprime le token et les infos
            localStorage.removeItem('auth_token')
            localStorage.removeItem('user')
            setUser(null)
        }

    }

    // on cree maintenant le contexte qui va etre partagé dans toute l'application
    // qui va contenir toutes les infos de l'utilisateur et les fonctions d'authentification
    const value = {
        user,
        loading,
        login,
        logout,          // true si connecté, false sinon
        // Vérifications de rôle
        isAdmin: user?.role === 'administrateur',
        isResponsable: user?.role === 'responsable',
        isAgent: user?.role === 'agent'
    }
    return (
        //la partie qui va permettre de forunir le contexte a toute l'application
        <AuthContext.Provider value={value}>
            {/* Dispo pour tous les composants enfants */}
            {children}

        </AuthContext.Provider>
    )

}
