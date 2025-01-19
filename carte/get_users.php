<?php
$host = 'localhost';
$dbname = 'sae301';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

function geocodeAddress($address) {
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&limit=1";

    // Définition d'un contexte avec l'en-tête User-Agent
    $context = stream_context_create([
        "http" => [
            "header" => "User-Agent: MyApp/1.0 (your_email@example.com)"
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);

    if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
        return [
            'latitude' => $data[0]['lat'],
            'longitude' => $data[0]['lon']
        ];
    }
    return null;
}

// Récupération des utilisateurs dans la base de données
$query = $pdo->query("SELECT id, name, email, adresse, role, latitude, longitude FROM users");
$users = $query->fetchAll(PDO::FETCH_ASSOC);

$geojson = [
    "type" => "FeatureCollection",
    "features" => []
];

foreach ($users as $user) {
    // S'il n'y a pas de coordonnées, on fais le géocodage
    if (empty($user['latitude']) || empty($user['longitude'])) {
        $coords = geocodeAddress($user['adresse']);
        if ($coords) {
            // Sauvegarder les coordonnées dans la base de données
            $stmt = $pdo->prepare("UPDATE users SET latitude = :latitude, longitude = :longitude WHERE id = :id");
            $stmt->execute([
                ':latitude' => $coords['latitude'],
                ':longitude' => $coords['longitude'],
                ':id' => $user['id']
            ]);
            // Ajouter des coordonnées au tableau de l'utilisateur
            $user['latitude'] = $coords['latitude'];
            $user['longitude'] = $coords['longitude'];
        }
    }

    // S'il y a des coordonnées, ajouter l'utilisateur à GeoJSON
    if (!empty($user['latitude']) && !empty($user['longitude'])) {
        $geojson["features"][] = [
            "type" => "Feature",
            "geometry" => [
                "type" => "Point",
                "coordinates" => [(float)$user['longitude'], (float)$user['latitude']]
            ],
            "properties" => [
                "name" => $user['name'],
                "email" => $user['email'],
                "adresse" => $user['adresse'],
                "role" => $user['role']
            ]
        ];
    }
}

// Renvoyer les données au format JSON
header('Content-Type: application/json');
echo json_encode($geojson);