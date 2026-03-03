document.addEventListener('DOMContentLoaded', function() {
    const tagInput = document.getElementById('tag-input');
    const tagsDisplay = document.getElementById('tags-display');
    const tagsHidden = document.getElementById('tags-hidden');
    let tags = [];

    // Ajouter un tag quand on appuie sur Entrée
    tagInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tagValue = tagInput.value.trim();
            if (tagValue !== '' && !tags.includes(tagValue)) {
                tags.push(tagValue);
                addTagToDisplay(tagValue);
                updateHiddenInput();
                tagInput.value = '';
            }
        }
    });

    // Afficher un tag avec son bouton de suppression
    function addTagToDisplay(tagText) {
        const tagItem = document.createElement('div');
        tagItem.className = 'tag-item';

        const tagSpan = document.createElement('span');
        tagSpan.textContent = tagText;

        const removeBtn = document.createElement('button');
        removeBtn.className = 'tag-remove';
        removeBtn.innerHTML = '×';
        removeBtn.type = 'button';
        removeBtn.onclick = function() {
            removeTag(tagText, tagItem);
        };

        tagItem.appendChild(tagSpan);
        tagItem.appendChild(removeBtn);
        tagsDisplay.appendChild(tagItem);
    }

    // Supprimer un tag
    function removeTag(tagText, tagElement) {
        tags = tags.filter(tag => tag !== tagText);
        tagElement.remove();
        updateHiddenInput();
    }

    // Mettre à jour le champ caché avec tous les tags
    function updateHiddenInput() {
        tagsHidden.value = tags.join(',');
    }

    // Mettre à jour le champ caché avec tous les tags
    function updateHiddenInput() {
        tagsHidden.value = tags.join(',');
    }

    // Affichage du coût du style sélectionné
    const styleSelect = document.getElementById('style-select');
    const stylePriceInfo = document.getElementById('style-price-info');

    function updateStylePrice() {
        const selected = styleSelect.options[styleSelect.selectedIndex];
        const price = parseInt(selected.getAttribute('data-price'), 10);
        if (price > 0) {
            stylePriceInfo.textContent = `+ ${price} DT₡ (style)`;
            stylePriceInfo.classList.add('style-price-active');
        } else {
            stylePriceInfo.textContent = 'Style gratuit';
            stylePriceInfo.classList.remove('style-price-active');
        }
    }

    styleSelect.addEventListener('change', updateStylePrice);
    updateStylePrice();
});