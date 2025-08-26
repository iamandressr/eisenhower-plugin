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

    fetch(moveTaskUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": csrfToken
        },
        body: JSON.stringify({
            task_id: parseInt(taskId),
            column_id: parseInt(columnId),
            swimlane_id: parseInt(swimlaneId),
            position: position
        })
    })
    .then(res => res.ok ? console.log('Task moved!') : alert('Error al mover tarea'))
    .then(res => res.ok ? console.log(columnId, swimlaneId, position) : null) // Solo para debug
    .catch(err => alert('Error de red', err));

    // Mover visualmente
    const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
    if (taskCard) {
        zone.appendChild(taskCard);
    }
}


