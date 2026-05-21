import React from 'react';
import { Link } from 'react-router-dom';

const RecentDossiers = ({ dossiers }) => {
    const getStatutBadge = (statut) => {
        const badges = {
            'en_attente': 'bg-yellow-100 text-yellow-800',
            'en_cours': 'bg-blue-100 text-blue-800',
            'valide': 'bg-green-100 text-green-800',
            'rejete': 'bg-red-100 text-red-800'
        };
        return badges[statut] || 'bg-gray-100 text-gray-800';
    };

    const getStatutLabel = (statut) => {
        const labels = {
            'en_attente': 'En attente',
            'en_cours': 'En cours',
            'valide': 'Validé',
            'rejete': 'Rejeté'
        };
        return labels[statut] || statut;
    };

    return (
        <div className="bg-white rounded-lg shadow p-6">
            <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold">Derniers dossiers</h3>
                <Link to="/dossiers" className="text-blue-600 text-sm hover:underline">Voir tout →</Link>
            </div>
            <div className="overflow-x-auto">
                <table className="min-w-full">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-2 text-left text-sm">Client</th>
                            <th className="px-4 py-2 text-left text-sm">Titre</th>
                            <th className="px-4 py-2 text-left text-sm">Agent</th>
                            <th className="px-4 py-2 text-left text-sm">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        {dossiers?.map((dossier) => (
                            <tr key={dossier.id} className="border-t">
                                <td className="px-4 py-2">
                                    <Link to={`/clients/${dossier.client?.id}`} className="text-blue-600 hover:underline">
                                        {dossier.client?.nom_complet}
                                    </Link>
                                </td>
                                <td className="px-4 py-2">
                                    <Link to={`/dossiers/${dossier.id}`} className="text-gray-800 hover:text-blue-600">
                                        {dossier.titre}
                                    </Link>
                                </td>
                                <td className="px-4 py-2">{dossier.agent?.nom_complet || 'Non affecté'}</td>
                                <td className="px-4 py-2">
                                    <span className={`px-2 py-1 rounded text-xs ${getStatutBadge(dossier.statut)}`}>
                                        {getStatutLabel(dossier.statut)}
                                    </span>
                                </td>
                            </tr>
                        ))}
                        {(!dossiers || dossiers.length === 0) && (
                            <tr>
                                <td colSpan="4" className="text-center py-4 text-gray-500">Aucun dossier</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default RecentDossiers;
