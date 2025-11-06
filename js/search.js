
document.getElementById('search-legumes').addEventListener('input', function () {
    const searchText = this.value.toLowerCase();
    
    const legumeSection = document.querySelectorAll('h4.text');
    let legumeContainer;

    legumeSection.forEach(section => {
        if (section.innerText.toLowerCase().includes('légume')) {
            legumeContainer = section.parentElement.querySelectorAll('.card-title');
        }
    });

    if (!legumeContainer) return;

    legumeContainer.forEach(title => {
        const card = title.closest('.card-wrapper');
        const titleText = title.innerText.toLowerCase();

        if (titleText.includes(searchText)) {
            card.style.display = 'inline-block';
        } else {
            card.style.display = 'none';
        }
    });
});

