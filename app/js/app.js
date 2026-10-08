const themeToggle = document.getElementById("themeToggle");

const savedTheme = localStorage.getItem("agenda-theme");

if (savedTheme === "dark") {
    document.body.classList.add("dark");
}

if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        document.body.classList.toggle("dark");

        if (document.body.classList.contains("dark")) {
            localStorage.setItem("agenda-theme", "dark");
        } else {
            localStorage.setItem("agenda-theme", "light");
        }
    });
}