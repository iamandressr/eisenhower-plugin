document.addEventListener("DOMContentLoaded", () => {
    // Desactivar draggable en todas las tareas
    document.querySelectorAll(".task-card").forEach(card => {
        card.removeAttribute("draggable");
        card.removeEventListener("dragstart", onDragStart);
    });

    // Remover eventos de drop y dragover en los cuadrantes
    document.querySelectorAll(".eisenhower-quadrant").forEach(zone => {
        zone.replaceWith(zone.cloneNode(true)); // esto elimina todos los listeners
    });
});


function onDragStart(event) {
    event.dataTransfer.setData("text/plain", event.currentTarget.dataset.taskId);
}

function onDragOver(event) {
    event.preventDefault();
}
 