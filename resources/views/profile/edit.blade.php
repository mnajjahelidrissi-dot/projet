<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>
</head>
<body>
    <h1>Modifier le profil</h1>
    <form method="POST" action="/profile">
        @csrf
        @method('PATCH')
        <input type="text" name="nom" value="{{ $user->nom }}">
        <input type="text" name="prenom" value="{{ $user->prenom }}">
        <input type="email" name="email" value="{{ $user->email }}">
        <input type="tel" name="telephone" value="{{ $user->telephone }}">
        <button type="submit">Mettre à jour</button>
    </form>

    <form method="POST" action="/profile" onsubmit="return confirm('Supprimer le compte ?')">
        @csrf
        @method('DELETE')
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Supprimer le compte</button>
    </form>
</body>
</html>
