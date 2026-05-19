import React from 'react';

const Charts = ({ repartition }) => {
    const types = {
        'ouverture_compte': 'Ouverture compte',
        'demande_carte': 'Carte bancaire',
        'demande_credit': 'Crédit',
        'reclamation': 'Réclamation',
        'autre': 'Autre'
    };

    return (
        <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-semibold mb-4">Répartition par type de demande</h3>
            <div className="space-y-3">
                {Object.entries(repartition || {}).map(([key, value]) => (
                    <div key={key}>
                        <div className="flex justify-between text-sm mb-1">
                            <span>{types[key] || key}</span>
                            <span>{value}</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-blue-600 h-2 rounded-full" style={{ width: `${Math.min(100, value * 10)}%` }}></div>
                        </div>
                    </div>
                ))}
                {Object.keys(repartition || {}).length === 0 && (
                    <p className="text-gray-500 text-center">Aucune donnée</p>
                )}
            </div>
        </div>
    );
};

export default Charts;
