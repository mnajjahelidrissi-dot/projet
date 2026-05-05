<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenue sur votre tableau de bord</h1>
    <p>Stats: {{ $stats['total_users'] ?? 0 }} utilisateurs</p>
    <a href="/profile">Profil</a>
    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Déconnexion</button>
    </form>
</body>
</html>
