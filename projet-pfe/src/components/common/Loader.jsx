//Animation de chargement pour les pages qui prennent du temps à se charger
import React from 'react';

const Loader = ({ size = 'md', color = 'blue' }) => {
    const sizes = {
        sm: 'w-4 h-4',
        md: 'w-8 h-8',
        lg: 'w-12 h-12'
    };

    return (
        <div className="flex justify-center items-center p-4">
            <div className={`${sizes[size]} border-4 border-${color}-200 border-t-${color}-600 rounded-full animate-spin`}></div>
        </div>
    );
};

export default Loader;
