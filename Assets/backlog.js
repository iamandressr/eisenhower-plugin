// Función para aplicar estilos a la columna "colapsada"
document.addEventListener("DOMContentLoaded", function () {
    const kanboardColumn = document.getElementById('kanboard-column');

    if (!kanboardColumn) return;

    const th = kanboardColumn.querySelector('th');
    if (th && th.classList.contains("board-column-header-collapsed")) {
        kanboardColumn.style.width = "28px";
        kanboardColumn.style.minWidth = "28px";
    }

    // Override método listen
    Kanboard.BoardColumnView.prototype.listen = function () {
        const backlogColumn = th?.dataset.columnId;
        const boardColumnCount = parseInt(kanboardColumn.dataset.nb_columns);
        const backlogColumnWidth = 100 / boardColumnCount;

        document.querySelectorAll(".board-toggle-column-view").forEach((toggleBtn) => {
            toggleBtn.addEventListener("click", function (event) {
                Kanboard.BoardColumn.toggle(this.dataset.columnId);

                if (this.dataset.columnId === backlogColumn) {
                    if (this.tagName === 'DIV') {
                        kanboardColumn.style.width = `${backlogColumnWidth}%`;
                        kanboardColumn.style.minWidth = "240px";
                    } else {
                        kanboardColumn.style.width = "28px";
                        kanboardColumn.style.minWidth = "28px";
                    }
                }
            });
        });
    };
});

// Drag & Drop sin jQuery
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
