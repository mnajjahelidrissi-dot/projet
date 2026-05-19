import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { dashboardService } from '../services/dashboardService';

const Dashboard = () => {
    const { user } = useAuth();
    const [stats, setStats] = useState({
        total_clients: 0,
        total_dossiers: 0,
        en_attente: 0,
        en_cours: 0
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadDashboard();
    }, []);

    const loadDashboard = async () => {
        setLoading(true);
        try {
            const response = await dashboardService.getAdminStats();
            setStats(response.stats);
        } catch (error) {
            console.error('Erreur chargement dashboard:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="p-6">
            <h1 className="text-2xl font-bold mb-6">Tableau de bord</h1>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-white rounded-lg shadow p-6">
                    <p className="text-gray-500">Total clients</p>
                    <p className="text-3xl font-bold">{stats.total_clients || 0}</p>
                </div>
                <div className="bg-white rounded-lg shadow p-6">
                    <p className="text-gray-500">Total dossiers</p>
                    <p className="text-3xl font-bold">{stats.total_dossiers || 0}</p>
                </div>
                <div className="bg-white rounded-lg shadow p-6">
                    <p className="text-gray-500">En attente</p>
                    <p className="text-3xl font-bold text-yellow-600">{stats.en_attente || 0}</p>
                </div>
                <div className="bg-white rounded-lg shadow p-6">
                    <p className="text-gray-500">En cours</p>
                    <p className="text-3xl font-bold text-blue-600">{stats.en_cours || 0}</p>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
