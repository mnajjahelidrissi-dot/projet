import React from 'react';

const StatsCards = ({ stats }) => {
    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-white rounded-lg shadow p-6">
                <p className="text-gray-500 text-sm">Total clients</p>
                <p className="text-3xl font-bold">{stats?.total_clients || 0}</p>
            </div>
            <div className="bg-white rounded-lg shadow p-6">
                <p className="text-gray-500 text-sm">Total dossiers</p>
                <p className="text-3xl font-bold">{stats?.total_dossiers || 0}</p>
            </div>
            <div className="bg-white rounded-lg shadow p-6">
                <p className="text-gray-500 text-sm">En attente</p>
                <p className="text-3xl font-bold text-yellow-600">{stats?.en_attente || 0}</p>
            </div>
            <div className="bg-white rounded-lg shadow p-6">
                <p className="text-gray-500 text-sm">En cours</p>
                <p className="text-3xl font-bold text-blue-600">{stats?.en_cours || 0}</p>
            </div>
        </div>
    );
};

export default StatsCards;
