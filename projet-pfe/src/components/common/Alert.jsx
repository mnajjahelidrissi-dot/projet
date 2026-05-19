// Dans UserForm.jsx
import Alert from '../common/Alert';

const UserForm = () => {
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    return (
        <>
            {error && <Alert type="error" message={error} />}
            {success && <Alert type="success" message={success} />}
            {/* formulaire */}
        </>
    );
};
