import React, { useState, useEffect } from 'react';
import { dashboardService } from '../../services/dashboardService';
import Loader from '../common/Loader';

const AgentDashboard = () => {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [myDossiers, setMyDossiers] = useState([]);

    useEffect(() => {
        loadAgentDashboard();
    }, []);

    const loadAgentDashboard = async () => {
        setLoading(true);
        try {
            const response = await dashboardService.getAgentStats();
            setStats(response.stats);
            setMyDossiers(response.my_dossiers);
        } catch (error) {
            console.error('Erreur chargement dashboard', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <Loader />;

    return (
        <div className="p-6">
            <h1 className="text-2xl font-bold mb-6">Mon tableau de bord</h1>

            {/* Cartes de statistiques personnelles */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white rounded-lg shadow p-6">
                    <p className="text-gray-500">Mes dossiers</p>
                    <p className="text-3xl font-bold">{stats?.mes_dossiers || 0}</p>
                </div>
                <div className="bg-white rounded-lg shadow p-6">
                    <p className="text-gray-500">En cours</p>
                    <p className="text-3xl font-bold text-blue-600">{stats?.mes_en_cours || 0}</p>
                </div>
                <div className="bg-white rounded-lg shadow p-6">
                    <p className="text-gray-500">En attente</p>
                    <p className="text-3xl font-bold text-yellow-600">{stats?.mes_en_attente || 0}</p>
                </div>
            </div>

            {/* Mes dossiers récents */}
            <div className="mt-6 bg-white rounded-lg shadow p-6">
                <h2 className="text-xl font-semibold mb-4">Mes dossiers récents</h2>
                <div className="overflow-x-auto">
                    <table className="min-w-full">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-4 py-2 text-left">Titre</th>
                                <th className="px-4 py-2 text-left">Client</th>
                                <th className="px-4 py-2 text-left">Statut</th>
                                <th className="px-4 py-2 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {myDossiers.map(dossier => (
                                <tr key={dossier.id} className="border-t">
                                    <td className="px-4 py-2">{dossier.titre}</td>
                                    <td className="px-4 py-2">{dossier.client?.nom_complet}</td>
                                    <td className="px-4 py-2">
                                        <span className={`px-2 py-1 rounded text-xs ${dossier.statut === 'en_cours' ? 'bg-blue-100 text-blue-800' :
                                                dossier.statut === 'valide' ? 'bg-green-100 text-green-800' :
                                                    'bg-yellow-100 text-yellow-800'
                                            }`}>
                                            {dossier.statut_label}
                                        </span>
                                    </td>
                                    <td className="px-4 py-2">{new Date(dossier.created_at).toLocaleDateString()}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default AgentDashboard;
