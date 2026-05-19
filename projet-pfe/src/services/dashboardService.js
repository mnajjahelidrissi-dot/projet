import api from './api';

export const dashboardService = {
    // Pour admin/responsable (statistiques globales)
    getAdminStats: () => api.get('/dashboard/admin'),

    // Pour agent (statistiques personnelles)
    getAgentStats: () => api.get('/dashboard/agent'),
};
