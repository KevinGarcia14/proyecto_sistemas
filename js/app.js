document.addEventListener("DOMContentLoaded", () => {
  const prevMonth = document.getElementById("prevMonth");
  const nextMonth = document.getElementById("nextMonth");
  const addReminder = document.getElementById("addReminder");
  const addNote = document.getElementById("addNote");

  // En esta fase el calendario es una representación visual.
  // La lógica real de fechas y persistencia se implementará en la ruta dinámica.
  prevMonth.addEventListener("click", () => {
    alert("Navegación de calendario: reservada para la siguiente fase.");
  });

  nextMonth.addEventListener("click", () => {
    alert("Navegación de calendario: reservada para la siguiente fase.");
  });

  addReminder.addEventListener("click", () => {
    alert("Agregar recordatorio: se implementará con almacenamiento en la ruta dinámica.");
  });

  addNote.addEventListener("click", () => {
    alert("Nueva nota: se implementará con almacenamiento en la ruta dinámica.");
  });
});
