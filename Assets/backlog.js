document.addEventListener("DOMContentLoaded", () => {
    const configEl = document.getElementById('eisenhower-config');
    const csrfToken = configEl.dataset.csrfToken;
    const moveTaskUrl = configEl.dataset.moveTaskUrl;
    const updatePriorityUrl = configEl.dataset.updatePriorityUrl;

    document.querySelectorAll(".task-card").forEach(card => {
        card.addEventListener("dragstart", onDragStart);
    });

    document.querySelectorAll(".eisenhower-quadrant").forEach(zone => {
        zone.addEventListener("dragover", onDragOver);
        zone.addEventListener("drop", async event => {
            event.preventDefault();
            const zoneEl = event.currentTarget;
            const newPriority = parseInt(zoneEl.dataset.priority, 10);
            const taskId = event.dataTransfer.getData("text/plain");

            const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
            if (!taskCard) return;

            const columnId = taskCard.dataset.columnId;
            const swimlaneId = taskCard.dataset.swimlaneId;
            const position = zoneEl.querySelectorAll('.task-card').length + 1;

            try {
                // 1. Mover tarea en Kanboard
                let res = await fetch(moveTaskUrl, {
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
                });
                if (!res.ok) throw new Error("Error al mover tarea");

                // 2. Actualizar prioridad
                res = await fetch(updatePriorityUrl, {
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
                if (!res.ok) throw new Error("Error al actualizar prioridad");

                // 3. Actualizar visualmente solo si todo salió bien
                zoneEl.appendChild(taskCard);
                taskCard.dataset.position = position;
                taskCard.dataset.priority = newPriority;

                console.log('Tarea movida y prioridad actualizada');
            } catch (err) {
                alert(err);
            }
        });
    });
});

function onDragStart(event) {
    event.dataTransfer.setData("text/plain", event.currentTarget.dataset.taskId);
}

function onDragOver(event) {
    event.preventDefault();
}
