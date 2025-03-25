<div class="card p-4 shadow-sm">
    <h5 class="mb-3">Répartition des Utilisateurs par Rôle</h5>
    <canvas id="usersChart"></canvas>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('usersChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Nombre d\'utilisateurs',
                    data: @json($data),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 99, 189, 0.6)',
                        'rgba(168, 234, 149, 0.6)',
                        'rgba(102, 30, 246, 0.77)',
                        'rgba(219, 243, 146, 0.6)'
                    ]
                }]

            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,  // ou une autre valeur si tu veux un ratio différent
            }
        });
    });
</script>
