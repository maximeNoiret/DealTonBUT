document.addEventListener("DOMContentLoaded", function() {
    var popup = document.getElementById("popup-message");
    if (popup) {
        setTimeout(function() {
            popup.classList.add("show");
        }, 100);

        setTimeout(function() {
            popup.classList.remove("show");
        }, 4000);
    }
});

function toggleDropdown() {
    // // take the elements
    // var dropdown = document.getElementById('sortDropdown');
    // // activate the dropdown menu
    // dropdown.classList.toggle('active');
    document.getElementById('sortDropdown').classList.toggle('active');

}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('sortDropdown');
    const sortButton = document.querySelector('.sort-button');

    if (!sortButton.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
    }
});

// Function to load more offers
function loadMoreOffers(offset, params) {
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const loadMoreContainer = document.querySelector('.load-more-container');

    // Disable button while loading
    loadMoreBtn.disabled = true;
    loadMoreBtn.textContent = 'Chargement...';

    // Build the URL with offset and other params
    const url = '/marketplace?offset=' + offset + params;

    // Fetch the new offers
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.text())
        .then(html => {
            // Parse the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Get the offer grid and new offers
            const newOfferGrid = doc.querySelector('#offer-grid');
            const newLoadMoreContainer = doc.querySelector('.load-more-container');
            const currentOfferGrid = document.querySelector('#offer-grid');

            if (newOfferGrid && currentOfferGrid) {
                // Append new offers to existing grid (including the <a> wrappers)
                const newOffers = newOfferGrid.children;
                // Convert HTMLCollection to Array to avoid issues with live collections
                Array.from(newOffers).forEach(offer => {
                    currentOfferGrid.appendChild(offer.cloneNode(true));
                });
            }

            // Update or remove the load more button
            if (newLoadMoreContainer) {
                loadMoreContainer.replaceWith(newLoadMoreContainer);
            } else {
                loadMoreContainer.remove();
            }
        })
        .catch(error => {
            console.error('Error loading more offers:', error);
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = 'Plus d\'offres ?';
            alert('Erreur lors du chargement des offres. Veuillez réessayer.');
        });
}