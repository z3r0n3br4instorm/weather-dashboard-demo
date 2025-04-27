// Dark mode functionality
document.addEventListener("DOMContentLoaded", () => {
    const darkModeToggle = document.getElementById("dark-mode-toggle");
    const htmlElement = document.documentElement;

    // Check for saved theme preference or use system preference
    const savedTheme = localStorage.getItem("theme");
    const systemPrefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)"
    ).matches;

    // Set initial theme
    if (savedTheme === "dark" || (!savedTheme && systemPrefersDark)) {
        htmlElement.classList.add("dark");
        if (darkModeToggle) {
            darkModeToggle.checked = true;
        }
    }

    // Toggle dark mode
    if (darkModeToggle) {
        darkModeToggle.addEventListener("change", () => {
            if (darkModeToggle.checked) {
                htmlElement.classList.add("dark");
                localStorage.setItem("theme", "dark");
            } else {
                htmlElement.classList.remove("dark");
                localStorage.setItem("theme", "light");
            }
        });
    }
});
