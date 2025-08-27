document.addEventListener("DOMContentLoaded", () => {
    const configEl = document.getElementById('eisenhower-config');
    window.eisenhowerConfig = {
        csrfToken: configEl.getAttribute('data-csrf-token'),
        updatePriorityUrl: configEl.getAttribute('data-update-priority-url'),
    };

    document.querySelectorAll(".task-card").forEach(card => {
        card.addEventListener("dragstart", onDragStart);
    });

    document.querySelectorAll(".eisenhower-quadrant").forEach(zone => {
        zone.addEventListener("dragover", onDragOver);
        zone.addEventListener("drop", handleDrop);
    });
});

function onDragStart(event) {
    const taskId = event.currentTarget.dataset.taskId;
    event.dataTransfer.setData("text/plain", taskId);
}

function onDragOver(event) {
    event.preventDefault();
}

function handleDrop(event) {
    const zone = event.currentTarget;
    const newPriority = parseInt(zone.dataset.priority, 10);
    onDrop(event, newPriority);
}

function onDrop(event, newPriority) {
    event.preventDefault();

    const taskId = event.dataTransfer.getData("text/plain");
    const zone = event.currentTarget;

    const columnId = zone.dataset.columnId;
    const swimlaneId = zone.dataset.swimlaneId;

    // Posición: al final del cuadrante
    const taskCardsInZone = Array.from(zone.querySelectorAll('.task-card'));
    const position = taskCardsInZone.length + 1;

    const csrfToken = window.eisenhowerConfig.csrfToken;
    const moveTaskUrl = document.getElementById('eisenhower-config').dataset.moveTaskUrl;

    fetch(document.getElementById('eisenhower-config').dataset.moveTaskUrl, {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": csrfToken
    },
    body: JSON.stringify({
        task_id: parseInt(taskId),
        column_id: parseInt(zone.dataset.columnId),
        swimlane_id: parseInt(zone.dataset.swimlaneId),
        position: position
    })
})
.then(res => {
    if (!res.ok) throw new Error("Error al mover tarea");
    // 👉 Después de mover, actualiza prioridad
    return fetch(window.eisenhowerConfig.updatePriorityUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": csrfToken
        },
        body: JSON.stringify({
            task_id: parseInt(taskId),
            priority: newPriority
        })
    });
})
.then(res => res.ok ? console.log('Tarea movida y prioridad actualizada') : alert('Error al actualizar prioridad'))
.catch(err => alert('Error de red: ' + err));


    // Mover visualmente
    const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
    if (taskCard) {
        zone.appendChild(taskCard);
    }
}