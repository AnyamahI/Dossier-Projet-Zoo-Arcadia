function expandSection(habitatId) {
  const habitat = document.getElementById(habitatId);
  const details = habitat.querySelector(".details");

  if (details.style.display === "none" || details.style.display === "") {
    details.style.display = "block";
    habitat.style.width = "600px";
    habitat.style.height = "400px";
  } else {
    details.style.display = "none";
    habitat.style.width = "300px";
    habitat.style.height = "200px";
  }
}
