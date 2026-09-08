/* assets/js/map.js - Leaflet Map Location Picker */

function initEcoLensMap(mapContainerId, initialLat, initialLng, initialCity) {
    const container = document.getElementById(mapContainerId);
    if (!container || typeof L === 'undefined') return;

    const lat = initialLat || 13.0827;
    const lng = initialLng || 80.2707;

    const map = L.map(mapContainerId).setView([lat, lng], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors | EcoLens Intelligence'
    }).addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-leaflet-marker',
        html: `<div style="background:#18A957; width:24px; height:24px; border-radius:50%; border:3px solid white; box-shadow:0 0 10px rgba(0,0,0,0.3);"></div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    const marker = L.marker([lat, lng], { draggable: true, icon: customIcon }).addTo(map);
    marker.bindPopup(`<b>${initialCity || 'Selected Location'}</b><br>Environmental Context Node`).openPopup();

    marker.on('dragend', function(e) {
        const coord = marker.getLatLng();
        updateLocationProfile(coord.lat, coord.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLocationProfile(e.latlng.lat, e.latlng.lng);
    });
}

function updateLocationProfile(lat, lng) {
    const infoDiv = document.getElementById('location-profile-details');
    if (infoDiv) {
        infoDiv.innerHTML = '<div class="spinner-border text-success" role="status"></div> Updating environmental profile...';
    }

    fetch(`${APP_URL}/api/location/profile.php?lat=${lat}&lng=${lng}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                window.location.reload();
            }
        });
}
