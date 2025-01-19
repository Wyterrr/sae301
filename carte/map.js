// centre sur Dijon
let carte = L.map('carte').setView([47.3165, 5.0165], 12);

// ajouter OpenStreetMap
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(carte);

// Chargement des données de l'utilisateur
fetch('get_users.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Afficher les utilisateurs sur la carte
        const userIcon = L.icon({
            iconUrl: 'media/user-icon.png',
            iconSize: [32, 32], // la taille
            iconAnchor: [16, 32], // Point d'ancrage
            popupAnchor: [0, -30] // Position de la fenêtre pop-up
        });
        
        // Appliquer des icônes aux utilisateurs
        L.geoJSON(data, {
            pointToLayer: function (feature, latlng) {
                return L.marker(latlng, { icon: userIcon });
            },
            onEachFeature: function (feature, layer) {
                const popupContent = `
                    <b>Nom:</b> ${feature.properties.name}<br>
                    <b>Email:</b> ${feature.properties.email}<br>
                    <b>Adresse:</b> ${feature.properties.adresse}<br>
                `;
                layer.bindPopup(popupContent);
            }
        }).addTo(carte);
        
    })
    .catch(error => console.error('Erreur de chargement des utilisateurs:', error));



// button - circle
function findSectorWithMostClients(features, radius) {
    let maxClients = 0;
    let bestSector = null;

    // Passer en revue tous les clients pour les utiliser comme centre du secteur
    features.forEach(centerFeature => {
        const centerLat = centerFeature.geometry.coordinates[1];
        const centerLon = centerFeature.geometry.coordinates[0];
        const center = L.latLng(centerLat, centerLon);

        let clientCount = 0;

        // Calculez le nombre de clients dans un rayon autour de ce centre.
        features.forEach(feature => {
            const lat = feature.geometry.coordinates[1];
            const lon = feature.geometry.coordinates[0];
            const distance = center.distanceTo(L.latLng(lat, lon));
            if (distance <= radius) {
                clientCount++;
            }
        });

        // Si c'est le meilleur secteur, mémorisez-le
        if (clientCount > maxClients) {
            maxClients = clientCount;
            bestSector = center;
        }
    });

    return { bestSector, maxClients };
}

let coverageCircle; // circle

document.getElementById('showCoverage').addEventListener('click', () => {
    // Retirer l'ancien cercle, le cas échéant
    if (coverageCircle) {
        carte.removeLayer(coverageCircle);
    }

    // Définir radius fixe
    const radius = 500;

    // Charger les données et rechercher le meilleur secteur
    fetch('get_users.php')
        .then(response => response.json())
        .then(data => {
            const { bestSector, maxClients } = findSectorWithMostClients(data.features, radius);

            if (bestSector) {
                // Ajouter un cercle pour un meilleur secteur
                coverageCircle = L.circle(bestSector, {
                    color: 'red',
                    fillColor: '#ff6666',
                    fillOpacity: 0.4,
                    radius: radius
                }).addTo(carte);

                // Déplacer la carte vers le secteur
                carte.setView(bestSector, 14);

                // Produire le nombre de clients dans le secteur
                alert(`Dans ce secteur. ${maxClients} clients.`);
            } else {
                alert("Aucun secteur correspondant n'a été trouvé.");
            }
        })
        .catch(error => console.error('Erreur dans le calcul du secteur:', error));
});


function findSectorWithMostClients(features, radius) {
    let maxClients = 0;
    let bestSector = null;

    // Passer en revue tous les clients pour les utiliser comme centre du secteur
    features.forEach(centerFeature => {
        const centerLat = centerFeature.geometry.coordinates[1];
        const centerLon = centerFeature.geometry.coordinates[0];
        const center = L.latLng(centerLat, centerLon);

        let clientCount = 0;

        // Calculez le nombre de clients situés dans un rayon autour de ce centre.
        features.forEach(feature => {
            const lat = feature.geometry.coordinates[1];
            const lon = feature.geometry.coordinates[0];
            const distance = center.distanceTo(L.latLng(lat, lon));
            if (distance <= radius) {
                clientCount++;
            }
        });

        // Si c'est le meilleur secteur, retenez-le
        if (clientCount > maxClients) {
            maxClients = clientCount;
            bestSector = center;
        }
    });

    return { bestSector, maxClients };
}
