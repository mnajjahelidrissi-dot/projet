<!DOCTYPE html>
<html>
<head>
    <title>Connexion - SAHAM BANK</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>SAHAM BANK</h1>
    <h2>Se connecter</h2>

    <!-- Affichage des erreurs de validation -->
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>

</body>
</html>
