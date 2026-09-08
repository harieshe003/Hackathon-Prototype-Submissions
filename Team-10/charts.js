/* assets/js/charts.js - EcoLens Data Visualization */

function renderEcoLensRadarChart(canvasId, thinaiScores) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    const labels = ['Kurinji (Mountain)', 'Mullai (Forest)', 'Marutham (Agri)', 'Neithal (Coastal)', 'Palai (Dryland)'];
    const data = [
        thinaiScores.kurinji ? thinaiScores.kurinji.score : 50,
        thinaiScores.mullai ? thinaiScores.mullai.score : 50,
        thinaiScores.marutham ? thinaiScores.marutham.score : 50,
        thinaiScores.neithal ? thinaiScores.neithal.score : 50,
        thinaiScores.palai ? thinaiScores.palai.score : 50
    ];

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'EcoLens 5 Impact Score',
                data: data,
                backgroundColor: 'rgba(24, 169, 87, 0.25)',
                borderColor: '#18A957',
                borderWidth: 3,
                pointBackgroundColor: '#063B5C',
                pointBorderColor: '#FFFFFF',
                pointHoverBackgroundColor: '#FFFFFF',
                pointHoverBorderColor: '#063B5C',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(209, 235, 225, 0.6)' },
                    grid: { color: 'rgba(209, 235, 225, 0.6)' },
                    pointLabels: {
                        font: { family: "'Outfit', sans-serif", size: 12, weight: '600' },
                        color: '#063B5C'
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}
