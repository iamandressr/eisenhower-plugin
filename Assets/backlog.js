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
    const csrfToken = window.eisenhowerConfig.csrfToken;
    const updatePriorityUrl = window.eisenhowerConfig.updatePriorityUrl;

    console.log('onDrop:', taskId, newPriority);

    fetch(updatePriorityUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": csrfToken
        },
        body: JSON.stringify({ task_id: taskId, priority: newPriority })
    })
    .then(res => res.ok ? console.log('Actualizado') : alert('Error al actualizar'))
    .catch(err => alert('Error de red', err));
}
