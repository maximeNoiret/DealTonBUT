// scrip for switch between the different admin panel options (users, offers, transactions).
// Used in views/User/AdminPanel/AdminPanel.html
document.addEventListener('DOMContentLoaded', function (){
    // Select the panels, by using their CSS class name
    const adminAccount = document.querySelector('.manage-account-panel');
    const adminOffer = document.querySelector('.manage-offer-panel');
    const adminTransaction = document.querySelector('.manage-transaction-panel');
    // Select the buttons, by using their id
    const userButton = document.querySelector('#account-button');
    const offerButton = document.querySelector('#offer-button');
    const transactionButton = document.querySelector('#transaction-button');

    // Function that show the selected panel and hide the others
    /**
     * Set the display of adminAccount to 'flex' and the others to 'none'
     */
    function showAccounts() {
        adminAccount.style.display = 'flex';
        adminOffer.style.display = 'none';
        adminTransaction.style.display = 'none';
    }
    /**
     * Set the display of adminOffer to 'flex' and the others to 'none'
     */
    function showOffers() {
        adminAccount.style.display = 'none';
        adminOffer.style.display = 'flex';
        adminTransaction.style.display = 'none';
    }
    /**
     * Set the display of adminTransaction to 'flex' and the others to 'none'
     */
    function showTransactions() {
        adminAccount.style.display = 'none';
        adminOffer.style.display = 'none';
        adminTransaction.style.display = 'flex';
    }

    // Add event listeners to the buttons
    userButton.addEventListener('click', showAccounts);
    offerButton.addEventListener('click', showOffers);
    transactionButton.addEventListener('click', showTransactions);

    // by default the accounts panel is shown
    showAccounts();
})