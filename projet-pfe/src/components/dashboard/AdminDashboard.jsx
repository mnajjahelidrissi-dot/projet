import React, { useState, useEffect } from 'react';
import { dashboardService } from '../../services/dashboardService';
import StatsCards from './StatsCards';
import Charts from './Charts';
import RecentDossiers from './RecentDossiers';
import TopAgents from './TopAgents';

const AdminDashboard = () => {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [repartition, setRepartition] = useState([]);
    const [recentDossiers, setRecentDossiers] = useState([]);
    const [topAgents, setTopAgents] = useState([]);

    useEffect(() => {
        loadDashboard();
    }, []);

    const loadDashboard = async () => {
        setLoading(true);
        try {
            const response = await dashboardService.getAdminStats();
            // Guard against unexpected API shape
            setStats(response?.stats ?? null);
            setRepartition(response?.repartition ?? []);
            setRecentDossiers(response?.recent_dossiers ?? []);
            setTopAgents(response?.top_agents ?? []);
        } catch (error) {
            console.error('Erreur chargement dashboard', error);
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

            <StatsCards stats={stats} />

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <Charts repartition={repartition} />
                <TopAgents agents={topAgents} />
            </div>

            <div className="mt-6">
                <RecentDossiers dossiers={recentDossiers} />
            </div>
        </div>
    );
};

export default AdminDashboard;
