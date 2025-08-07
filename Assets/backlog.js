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

    function createTask(event, form) {
    event.preventDefault();

    const projectId = form.getAttribute('data-project-id');
    const priority = form.getAttribute('data-priority');
    const titleInput = form.querySelector('input[name="title"]');
    const title = titleInput.value.trim();

    if (!title) {
        alert('El título es obligatorio');
        return false;
    }

    const csrfToken = document.getElementById('eisenhower-config').getAttribute('data-csrf-token');
    const url = document.getElementById('eisenhower-config').getAttribute('data-update-priority-url').replace('updatePriority', 'createTask');

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
            project_id: parseInt(projectId),
            title: title,
            priority: parseInt(priority)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.id) {
            // Crear nuevo div task-card
            const container = form.parentNode;
            const taskCard = document.createElement('div');
            taskCard.className = 'task-card';
            taskCard.setAttribute('draggable', 'true');
            taskCard.setAttribute('data-task-id', data.id);

            // Crear link a la tarea
            const link = document.createElement('a');
            link.href = `/task/${data.id}`; // Ajusta URL si es necesario
            link.innerHTML = `<strong>${data.title}</strong>`;
            taskCard.appendChild(link);

            if (data.assignee_name) {
                const small = document.createElement('small');
                small.textContent = `Asignado a: ${data.assignee_name}`;
                taskCard.appendChild(small);
            }

            container.appendChild(taskCard);

            // Limpiar input
            titleInput.value = '';
        } else {
            alert('Error al crear tarea');
        }
    })
    .catch(() => alert('Error al crear tarea'));

    return false;
}

}
