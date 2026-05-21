import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import './stylecommon/Sidebar.css';



const Sidebar = () => {
    const location = useLocation();
    const { user } = useAuth();
    const isAdmin = user?.role === 'administrateur';
    const isAdminOrResponsable = user?.role === 'administrateur' || user?.role === 'responsable';

    const isActive = (path) => location.pathname === path;

    return (

        <aside className="sidebar">
            <nav className="sidebar-nav">
                <Link
                    to="/dashboard"
                    className={`nav-item ${isActive('/dashboard') ? 'active' : ''}`}
                >
                    Tableau de bord
                </Link>

                {(isAdminOrResponsable) && (
                    <Link
                        to="/clients"
                        className={`nav-item ${isActive('/clients') ? 'active' : ''}`}
                    >
                        Clients
                    </Link>
                )}

                <Link
                    to="/dossiers"
                    className={`nav-item ${isActive('/dossiers') ? 'active' : ''}`}
                >
                    Dossiers
                </Link>

                {isAdmin && (
                    <Link
                        to="/utilisateurs"
                        className={`nav-item ${isActive('/utilisateurs') ? 'active' : ''}`}
                    >
                        Utilisateurs
                    </Link>
                )}
            </nav>
        </aside>
    );
};


export default Sidebar;
