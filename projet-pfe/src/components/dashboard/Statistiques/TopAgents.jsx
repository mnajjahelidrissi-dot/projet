import React from 'react';

const TopAgents = ({ agents }) => {
    return (
        <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-semibold mb-4">Top 5 agents</h3>
            <div className="space-y-3">
                {agents?.map((agent, index) => (
                    <div key={agent.id} className="flex justify-between items-center">
                        <div className="flex items-center gap-3">
                            <div className="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold">
                                {index + 1}
                            </div>
                            <span>{agent.nom_complet}</span>
                        </div>
                        <span className="text-sm text-gray-600">{agent.dossiers_count} dossiers</span>
                    </div>
                ))}
                {(!agents || agents.length === 0) && (
                    <p className="text-gray-500 text-center">Aucune donnée</p>
                )}
            </div>
        </div>
    );
};

export default TopAgents;

