// scrip for switch between the different admin panel options (users, offers, transactions).
// Used in views/User/AdminPanel/AdminPanel.html
document.addEventListener('DOMContentLoaded', function (){
    // Select the panels, by using their CSS class name
    const adminAccount = document.querySelector('.manage-account-panel');
    const adminOffer = document.querySelector('.manage-offer-panel');
    // SCRAPPED
    // const adminTransaction = document.querySelector('.manage-transaction-panel');

    // Select the buttons, by using their id
    const userButton = document.querySelector('#account-button');
    const offerButton = document.querySelector('#offer-button');
    // SCRAPPED
    // const transactionButton = document.querySelector('#transaction-button');

    // Function that show the selected panel and hide the others
    /**
     * Set the display of adminAccount to 'flex' and the others to 'none'
     */
    function showAccounts() {
        adminAccount.style.display = 'flex';
        adminOffer.style.display = 'none';
        // adminTransaction.style.display = 'none';
    }
    /**
     * Set the display of adminOffer to 'flex' and the others to 'none'
     */
    function showOffers() {
        adminAccount.style.display = 'none';
        adminOffer.style.display = 'flex';
        // adminTransaction.style.display = 'none';
    }

    // SCRAPPED
    // /**
    //  * Set the display of adminTransaction to 'flex' and the others to 'none'
    //  */
    // function showTransactions() {
    //     adminAccount.style.display = 'none';
    //     adminOffer.style.display = 'none';
    //     adminTransaction.style.display = 'flex';
    // }

    // Add event listeners to the buttons
    userButton.addEventListener('click', showAccounts);
    offerButton.addEventListener('click', showOffers);
    // SCRAPPED
    // transactionButton.addEventListener('click', showTransactions);

    // by default the accounts panel is shown
    showAccounts();
})

function loadMoreOffersAdmin(offset, params) {
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const loadMoreContainer = document.querySelector('.load-more-container');

    // Disable button while loading
    loadMoreBtn.disabled = true;
    loadMoreBtn.textContent = 'Chargement...';

    // Build the URL with offset and other params
    const url = '/admin?offset=' + offset + params;

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