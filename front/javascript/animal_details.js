function showAnimalDetails(animalId) {
    document.getElementById("animalDetailsContent").innerHTML = "<p class='text-center'>Chargement...</p>";

    fetch(`/front/html/get_animal_details.php?id=` + animalId)
        .then(response => response.json())
        .then(data => {
            console.log("Réponse du serveur :", data);

            if (data.error) {
                document.getElementById("animalDetailsContent").innerHTML = "<p class='text-danger'>" + data.error + "</p>";
                return;
            }
            
            let animal = data.animal;
            let reports = data.reports;

            let html = `
                <h4 class="text-center">${animal.name}</h4>
                <p><strong>Espèce :</strong> ${animal.species_name ?? "Non définie"}</p>
                <p><strong>Habitat :</strong> ${animal.habitat_name ?? "Non défini"}</p>
                
                <div class="text-center">
                    <img src="${animal.animal_image ?? '/images/default.jpg'}" alt="${animal.name}" class="img-fluid rounded" style="max-width: 100%;">
                </div>

                <h5 class="mt-3">🌿 Habitat</h5>
                <div class="text-center">
                    <img src="${animal.habitat_image ?? '/images/default-habitat.jpg'}" alt="${animal.habitat_name}" class="img-fluid rounded" style="max-width: 100%;">
                </div>

                <h5 class="mt-3">🩺 Derniers Rapports Vétérinaires</h5>
            `;

            if (reports.length > 0) {
                reports.forEach(report => {
                    html += `
                        <div class="border p-2 mb-2">
                            <p><strong>📅 Visite :</strong> ${new Date(report.visit_date).toLocaleDateString()}</p>
                            <p><strong>État :</strong> ${report.state}</p>
                            <p><strong>Nourriture :</strong> ${report.food}</p>
                            <p><strong>Poids :</strong> ${report.weight}g</p>
                            <p><strong>Observations :</strong> ${report.details || "Aucune remarque"}</p>
                            <p class="text-muted">👨‍⚕️ Dr. ${report.vet_name}</p>
                        </div>
                    `;
                });
            } else {
                html += `<p class="text-muted">Aucun rapport vétérinaire disponible.</p>`;
            }

            document.getElementById("animalDetailsContent").innerHTML = html;
        })
        .catch(error => {
            document.getElementById("animalDetailsContent").innerHTML = "<p class='text-danger'>❌ Erreur de chargement.</p>";
        });

    var modal = new bootstrap.Modal(document.getElementById("animalDetailsModal"));
    modal.show();
}
