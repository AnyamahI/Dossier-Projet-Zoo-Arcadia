document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".voir-details").forEach(button => {
        button.addEventListener("click", function () {
            let habitatId = this.dataset.id;
            let detailsDiv = document.getElementById("details-" + habitatId);
            let currentButton = this;

            if (detailsDiv.classList.contains("open")) {
                detailsDiv.innerHTML = "";
                detailsDiv.classList.remove("open");
                currentButton.textContent = "Voir plus";
            } else {
                fetch("/back/php/get_habitat_details.php?id=" + habitatId)
                    .then(response => response.text())
                    .then(data => {
                        detailsDiv.innerHTML = data;

                        // Ajouter la croix de fermeture
                        let closeBtn = document.createElement('button');
                        closeBtn.className = "btn-close position-absolute top-0 end-0 m-2";
                        detailsDiv.prepend(closeBtn);

                        closeBtn.addEventListener('click', function () {
                            detailsDiv.innerHTML = "";
                            detailsDiv.classList.remove("open");
                            currentButton.style.display = 'inline-block'; // Réafficher le bouton
                            currentButton.textContent = "Voir plus";
                        });

                        detailsDiv.classList.add("open");
                        currentButton.style.display = 'none'; // Masquer le bouton après ouverture
                    })
                    .catch(error => console.error("Erreur AJAX :", error));
            }
        });
    });
});
