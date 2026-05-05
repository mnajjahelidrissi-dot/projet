<!DOCTYPE html>
<html>
<head>
    <title>Confirmer mot de passe</title>
</head>
<body>
    <h1>Confirmer votre mot de passe</h1>
    <form method="POST" action="/confirm-password">
        @csrf
        <input type="password" name="password" required>
        <button type="submit">Confirmer</button>
    </form>
</body>
</html>
