document.addEventListener("DOMContentLoaded", function () {
    var swiper = new Swiper(".swiper-container", {
        slidesPerView: 3, // Nombre d'animaux visibles en même temps
        centeredSlides: true, // Centre l'élément actif
        spaceBetween: 50, // Espacement entre les animaux
        grabCursor: true, // Curseur "drag"
        loop: true, // 🔄 Carrousel infini
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        breakpoints: {
            768: {
                slidesPerView: 3,
                spaceBetween: 60
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 80
            }
        },
        on: {
            slideChangeTransitionEnd: function () {
                let slides = document.querySelectorAll(".swiper-slide img");

                // 🔹 Réinitialiser toutes les images à une taille normale
                slides.forEach(img => img.style.transform = "scale(1)");

                // 🔹 Sélectionner **l'élément vraiment centré**
                let activeSlide = document.querySelector(".swiper-slide-active"); // Récupère la vraie slide active

                if (activeSlide) {
                    let activeImg = activeSlide.querySelector("img");
                    if (activeImg) {
                        activeImg.style.transform = "scale(1.4)"; // 🔥 Zoom sur l'image centrale
                    }
                }
            }
        }
    });
});
