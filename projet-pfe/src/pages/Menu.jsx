import { Link } from 'react-router-dom';

function Menu() {
    return (
        <nav style={{ padding: '20px', background: '#f0f0f0' }}>
            <Link to="/" style={{ marginRight: '15px' }}>Accueil</Link>
            <Link to="/a-propos">À propos</Link>
        </nav>
    );
}
