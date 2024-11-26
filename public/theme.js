document.addEventListener("DOMContentLoaded", function() {
    const themeToggleButton = document.querySelector(".theme-toggle");

    themeToggleButton.addEventListener("click", function() {
        // Détecter l'état actuel du thème
        const currentTheme = document.body.classList.contains("dark-theme") ? "dark" : "light";
        const newTheme = (currentTheme === "dark") ? "light" : "dark";

        // Basculer entre les classes de thème
        document.body.classList.toggle("dark-theme", newTheme === "dark");
        document.body.classList.toggle("light-theme", newTheme === "light");

        // Changer le texte du bouton
        themeToggleButton.textContent = (newTheme === "dark") ? "Basculer vers Clair" : "Basculer vers Sombre";

        // Envoyer la nouvelle préférence de thème au serveur via AJAX
        fetch("update_theme.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ theme: newTheme }),
        }).then(response => {
            if (!response.ok) {
                console.error("Erreur lors de la mise à jour du thème.");
            }
        });
    });
});
