//Barre de navigation commune à toutes les pages
import React from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { Link, useNavigate } from 'react-router-dom';

const Header = () => {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    return (
        <header className="bg-white shadow-md">

            <div className="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">

                {/* Logo */}
                <Link to="/dashboard" className="flex items-center gap-2">
                    <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span className="text-white font-bold text-lg">S</span>
                    </div>
                    <span className="font-bold text-xl text-blue-600">SAHAM Bank</span>
                </Link>

                {/* Infos utilisateur */}
                <div className="flex items-center gap-4">
                    <span className="text-gray-700">
                        {user?.prenom} {user?.nom}
                    </span>
                    <button
                        onClick={handleLogout}
                        className="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-colors"
                    >
                        Déconnexion
                    </button>
                </div>
            </div>
        </header>
    );
};

export default Header;
