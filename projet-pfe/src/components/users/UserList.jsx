import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { userService } from '../../services/userService';
import { useAuth } from '../../contexts/AuthContext';
import toast from 'react-hot-toast';

const UserList = () => {
    const { user: currentUser } = useAuth();
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [roleFilter, setRoleFilter] = useState('');
    const [pagination, setPagination] = useState({
        current_page: 1,
        last_page: 1,
        total: 0
    });

    useEffect(() => {
        loadUsers();
    }, [search, roleFilter]);

    const loadUsers = async () => {
        setLoading(true);
        try {
            const params = {};
            if (search) params.search = search;
            if (roleFilter) params.role = roleFilter;

            const response = await userService.getAll(params);
            setUsers(response.data || []);
            setPagination({
                current_page: response.current_page || 1,
                last_page: response.last_page || 1,
                total: response.total || 0
            });
        } catch (error) {
            toast.error('Erreur lors du chargement des utilisateurs');
        } finally {
            setLoading(false);
        }
    };

    const handleToggleStatus = async (userId, currentStatus) => {
        const action = currentStatus ? 'désactiver' : 'activer';
        if (!window.confirm(`Voulez-vous ${action} cet utilisateur ?`)) return;

        try {
            await userService.toggleStatus(userId);
            toast.success(`Utilisateur ${action} avec succès`);
            loadUsers();
        } catch (error) {
            toast.error('Erreur lors du changement de statut');
        }
    };

    const getRoleBadge = (role) => {
        const badges = {
            'administrateur': 'bg-red-100 text-red-800',
            'responsable': 'bg-yellow-100 text-yellow-800',
            'agent': 'bg-blue-100 text-blue-800'
        };
        return badges[role] || 'bg-gray-100 text-gray-800';
    };

    const getRoleLabel = (role) => {
        const labels = {
            'administrateur': 'Administrateur',
            'responsable': 'Responsable',
            'agent': 'Agent'
        };
        return labels[role] || role;
    };

    return (
        <div className="p-6">
            {/* En-tête */}
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-2xl font-bold text-gray-800">
                    <i className="fas fa-users mr-2"></i>
                    Gestion des utilisateurs
                </h1>
                <Link
                    to="/utilisateurs/creer"
                    className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <i className="fas fa-plus mr-2"></i>
                    Nouvel utilisateur
                </Link>
            </div>

            {/* Filtres */}
            <div className="bg-white rounded-lg shadow-md p-4 mb-6">
                <div className="flex flex-wrap gap-4">
                    <div className="flex-1 min-w-[200px]">
                        <input
                            type="text"
                            placeholder="Rechercher par nom, email..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div className="w-48">
                        <select
                            value={roleFilter}
                            onChange={(e) => setRoleFilter(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Tous les rôles</option>
                            <option value="administrateur">Administrateur</option>
                            <option value="responsable">Responsable</option>
                            <option value="agent">Agent</option>
                        </select>
                    </div>
                    <button
                        onClick={() => {
                            setSearch('');
                            setRoleFilter('');
                        }}
                        className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                    >
                        Réinitialiser
                    </button>
                </div>
            </div>

            {/* Tableau des utilisateurs */}
            <div className="bg-white rounded-lg shadow-md overflow-hidden">
                {loading ? (
                    <div className="flex justify-center items-center py-12">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Utilisateur
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rôle
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Téléphone
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date création
                                    </th>
                                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {users.map((user) => (
                                    <tr key={user.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center">
                                                <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span className="text-blue-600 font-medium">
                                                        {user.prenom?.charAt(0)}{user.nom?.charAt(0)}
                                                    </span>
                                                </div>
                                                <div className="ml-4">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {user.prenom} {user.nom}
                                                    </div>
                                                    {user.id === currentUser?.id && (
                                                        <span className="text-xs text-gray-500">(Vous)</span>
                                                    )}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {user.email}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className={`px-2 py-1 text-xs rounded-full ${getRoleBadge(user.role)}`}>
                                                {getRoleLabel(user.role)}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {user.telephone || '—'}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className={`px-2 py-1 text-xs rounded-full ${user.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                {user.actif ? 'Actif' : 'Inactif'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {new Date(user.created_at).toLocaleDateString()}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                to={`/utilisateurs/${user.id}/modifier`}
                                                className="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Modifier
                                            </Link>
                                            {user.id !== currentUser?.id && (
                                                <button
                                                    onClick={() => handleToggleStatus(user.id, user.actif)}
                                                    className={`${user.actif ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900'}`}
                                                >
                                                    {user.actif ? 'Désactiver' : 'Activer'}
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                {/* Pagination */}
                {pagination.last_page > 1 && (
                    <div className="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                        <div className="text-sm text-gray-500">
                            Page {pagination.current_page} sur {pagination.last_page}
                        </div>
                        <div className="flex gap-2">
                            <button
                                onClick={() => setPagination({ ...pagination, current_page: pagination.current_page - 1 })}
                                disabled={pagination.current_page === 1}
                                className="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 hover:bg-gray-100"
                            >
                                Précédent
                            </button>
                            <button
                                onClick={() => setPagination({ ...pagination, current_page: pagination.current_page + 1 })}
                                disabled={pagination.current_page === pagination.last_page}
                                className="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 hover:bg-gray-100"
                            >
                                Suivant
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default UserList;
