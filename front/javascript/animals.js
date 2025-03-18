document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const cards = document.querySelectorAll(".animal-card");

  searchInput.addEventListener("keyup", function () {
    let filter = searchInput.value.toLowerCase();

    cards.forEach((card) => {
      let name = card.querySelector(".card-title").innerText.toLowerCase();
      if (name.includes(filter)) {
        card.classList.remove("d-none"); // ✅ Affiche l'élément
      } else {
        card.classList.add("d-none"); // ❌ Masque l'élément
      }
    });
  });
});
