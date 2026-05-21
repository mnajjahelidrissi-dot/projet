import React from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import './stylecommon/Header.css';
import sahamlogo from '../../assets/saham-bank-logo.svg';

const Header = () => {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();

    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    const isActive = (path) => location.pathname === path;

    const isAdmin = user?.role === 'administrateur';
    const isAdminOrResponsable = user?.role === 'administrateur' || user?.role === 'responsable';

    return (
        <header className="header">
            <div className="header-container">
                {/* Logo */}
                <Link to="/dashboard" className="logo-link">
                    <img src={sahamlogo} alt="SAHAM Logo" />
                </Link>

                {/* Menu horizontal */}
                <nav className="nav-menu">
                    <Link
                        to="/dashboard"
                        className={`nav-link ${isActive('/dashboard') ? 'active' : ''}`}
                    >
                        Tableau de bord
                    </Link>

                    {isAdminOrResponsable && (
                        <Link
                            to="/clients"
                            className={`nav-link ${isActive('/clients') ? 'active' : ''}`}
                        >
                            Clients
                        </Link>
                    )}

                    <Link
                        to="/dossiers"
                        className={`nav-link ${isActive('/dossiers') ? 'active' : ''}`}
                    >
                        Dossiers
                    </Link>

                    {isAdmin && (
                        <Link
                            to="/utilisateurs"
                            className={`nav-link ${isActive('/utilisateurs') ? 'active' : ''}`}
                        >
                            Utilisateurs
                        </Link>
                    )}
                </nav>

                {/* Bouton Déconnexion - toujours en haut à droite */}
                <button onClick={handleLogout} className="logout-button">
                    Déconnexion
                </button>
            </div>
        </header>
    );
};

export default Header;
