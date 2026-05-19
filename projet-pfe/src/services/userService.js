import api from './api';

export const userService = {
    // Récupérer tous les utilisateurs
    getAll: async (params = {}) => {
        const response = await api.get('/utilisateurs', { params });
        return response.data;
    },

    // Récupérer un utilisateur par son ID
    getById: async (id) => {
        const response = await api.get(`/utilisateurs/${id}`);
        return response.data;
    },

    // Créer un utilisateur
    create: async (data) => {
        const response = await api.post('/utilisateurs', data);
        return response.data;
    },

    // Modifier un utilisateur
    update: async (id, data) => {
        const response = await api.put(`/utilisateurs/${id}`, data);
        return response.data;
    },

    // Activer/Désactiver un utilisateur
    toggleStatus: async (id) => {
        const response = await api.post(`/utilisateurs/${id}/toggle-status`);
        return response.data;
    }
};
