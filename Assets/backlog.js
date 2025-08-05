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
        zone.addEventListener("drop", (event) => {
            const newPriority = parseInt(zone.dataset.priority);
            onDrop(event, newPriority);
        });
    });
});

function onDragStart(event) {
    const taskId = event.currentTarget.dataset.taskId;
    event.dataTransfer.setData("text/plain", taskId);
}

function onDragOver(event) {
    event.preventDefault();
}

function onDrop(event, newPriority) {
    event.preventDefault();

    const taskId = event.dataTransfer.getData("text/plain");
    const csrfToken = window.eisenhowerConfig.csrfToken;
    const updatePriorityUrl = window.eisenhowerConfig.updatePriorityUrl;

    fetch(updatePriorityUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": csrfToken
        },
        body: JSON.stringify({ task_id: taskId, priority: newPriority })
    })
    .then(res => {
        if (res.ok) {
            location.reload();
        } else {
            alert("Error al actualizar la prioridad");
        }
    });
}
