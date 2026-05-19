//ce fichier va communiquer avec le backend laravel
import axios from 'axios'

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    withCredentials: false// pour envoyer les cookies d'authentification
})
//intecepteur de requete:qui va coller le token d'authentification a chaque requete
api.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
}, error => {
    return Promise.reject(error)
})
//intecepteur de reponse:qui va detecter les erreurs d'authentification
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Gérer l'erreur d'authentification
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            //Redirection vers la page de connexion
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);
console.log('API baseURL:', api.defaults.baseURL);
export default api
