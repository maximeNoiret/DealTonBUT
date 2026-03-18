//
document.addEventListener('DOMContentLoaded', function() {
    const titleOffer = document.querySelector('.section-title-offer');
    const titleBought = document.querySelector('.section-title-bought');
    const offerSection = document.querySelector('.user-offer-section');
    const boughtSection = document.querySelector('.user-bought-offer-section');

    // Fonction pour afficher "Mes offres"
    function showOffers() {
        // Afficher/masquer les sections
        offerSection.style.display = 'grid';
        boughtSection.style.display = 'none';

        // Mettre à jour les styles des titres
        titleOffer.classList.add('active');
        titleBought.classList.remove('active');
    }

    // Fonction pour afficher "Offres achetées"
    function showBought() {
        // Afficher/masquer les sections
        offerSection.style.display = 'none';
        boughtSection.style.display = 'grid';

        // Mettre à jour les styles des titres
        titleOffer.classList.remove('active');
        titleBought.classList.add('active');
    }

    // Fonction pour gérer le mode (mobile/desktop)
    function handleResize() {
        if (window.innerWidth <= 768) {
            // Mode mobile : activer le basculement
            titleOffer.style.cursor = 'pointer';
            titleBought.style.cursor = 'pointer';
            showOffers(); // Initialiser avec "Mes offres"
        } else {
            // Mode desktop : afficher les deux sections
            offerSection.style.display = 'grid';
            boughtSection.style.display = 'grid';
            titleOffer.classList.remove('active');
            titleBought.classList.remove('active');
            titleOffer.style.cursor = 'default';
            titleBought.style.cursor = 'default';
        }
    }

    // Ajouter les écouteurs d'événements
    titleOffer.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            showOffers();
        }
    });

    titleBought.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            showBought();
        }
    });

    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', handleResize);

    // Initialiser
    handleResize();
});