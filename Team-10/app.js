/* assets/js/app.js - EcoLens Global Script & Location Intelligence */

document.addEventListener('DOMContentLoaded', () => {
    // Counter Animation for Numbers
    const counters = document.querySelectorAll('.animate-counter');
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const suffix = counter.getAttribute('data-suffix') || '';
        const duration = 1500;
        const stepTime = 20;
        const steps = duration / stepTime;
        const increment = target / steps;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.innerText = target.toLocaleString() + suffix;
                clearInterval(timer);
            } else {
                counter.innerText = Math.ceil(current).toLocaleString() + suffix;
            }
        }, stepTime);
    });

    // Check if location permission prompt modal should be shown
    if (!localStorage.getItem('ecolens_loc_prompted') && typeof bootstrap !== 'undefined') {
        const modalEl = document.getElementById('locationPermissionModal');
        if (modalEl) {
            const locModal = new bootstrap.Modal(modalEl);
            locModal.show();
        }
    }

    // Direct Permission Request Handler
    const allowLocBtns = document.querySelectorAll('.btn-allow-location');
    allowLocBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            requestUserLocation(btn);
        });
    });
});

function requestUserLocation(btnElement) {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
    }

    const originalText = btnElement ? btnElement.innerHTML : '';
    if (btnElement) {
        btnElement.disabled = true;
        btnElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Detecting GPS...';
    }

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            
            localStorage.setItem('ecolens_loc_prompted', 'true');

            fetch(`${APP_URL}/api/location/profile.php?lat=${lat}&lng=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        const modalEl = document.getElementById('locationPermissionModal');
                        if (modalEl && typeof bootstrap !== 'undefined') {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) modalInstance.hide();
                        }
                        window.location.reload();
                    } else {
                        alert('Could not resolve location environment profile.');
                    }
                })
                .catch(() => {
                    alert('Network error requesting location profile.');
                })
                .finally(() => {
                    if (btnElement) {
                        btnElement.disabled = false;
                        btnElement.innerHTML = originalText;
                    }
                });
        },
        (err) => {
            let msg = 'Geolocation permission denied or unavailable.';
            if (err.code === err.PERMISSION_DENIED) {
                msg = 'Location permission was denied. You can manually select a city anytime from the Location page.';
            }
            alert(msg);
            localStorage.setItem('ecolens_loc_prompted', 'true');
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = originalText;
            }
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}
