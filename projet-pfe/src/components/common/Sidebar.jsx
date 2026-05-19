import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

const Sidebar = () => {
    const location = useLocation();
    const { user } = useAuth();
    const isAdmin = user?.role === 'administrateur';
    const isAdminOrResponsable = user?.role === 'administrateur' || user?.role === 'responsable';

    const isActive = (path) => location.pathname === path;

    return (


        <aside className="w-64 bg-gray-800 text-white flex-shrink-0">

            <nav className="p-4">
                <Link
                    to="/dashboard"
                    className={`block py-2 px-4 rounded mb-1 ${isActive('/dashboard') ? 'bg-blue-600' : 'hover:bg-gray-700'}`}
                >
                    📊 Tableau de bord
                </Link>

                {(isAdminOrResponsable) && (
                    <Link
                        to="/clients"
                        className={`block py-2 px-4 rounded mb-1 ${isActive('/clients') ? 'bg-blue-600' : 'hover:bg-gray-700'}`}
                    >
                        👥 Clients
                    </Link>
                )}

                <Link
                    to="/dossiers"
                    className={`block py-2 px-4 rounded mb-1 ${isActive('/dossiers') ? 'bg-blue-600' : 'hover:bg-gray-700'}`}
                >
                    📁 Dossiers
                </Link>

                {isAdmin && (
                    <Link
                        to="/utilisateurs"
                        className={`block py-2 px-4 rounded mt-4 ${isActive('/utilisateurs') ? 'bg-blue-600' : 'hover:bg-gray-700'}`}
                    >
                        👤 Utilisateurs
                    </Link>
                )}
            </nav>
        </aside>
    );
};

export default Sidebar;
