document.addEventListener("DOMContentLoaded", function() {
    const darkModeToggles = document.querySelectorAll('#ht-darkmode-toggle');
	if (!darkModeToggles) return;
    darkModeToggles.forEach(function(darkModeToggle) {
        const moonIcon = darkModeToggle.querySelector('#ht-icon-moon');
        const sunIcon = darkModeToggle.querySelector('#ht-icon-sun');
        if (localStorage.getItem('darkmode') === 'enabled') {
            DarkReader.enable({
                brightness: 90,
                contrast: 105,
                sepia: 15
            });
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
            darkModeToggle.classList.remove('ht-sunmode');
        } else {
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
            darkModeToggle.classList.add('ht-sunmode');
        }
        darkModeToggle.addEventListener('click', function() {
            if (DarkReader.isEnabled()) {
                DarkReader.disable();
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
                darkModeToggle.classList.add('ht-sunmode');
                localStorage.setItem('darkmode', 'disabled');
            } else {
                DarkReader.enable({
                    brightness: 90,
                    contrast: 105,
                    sepia: 15
                });
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
                darkModeToggle.classList.remove('ht-sunmode');
                localStorage.setItem('darkmode', 'enabled');
            }
        });
    });
});