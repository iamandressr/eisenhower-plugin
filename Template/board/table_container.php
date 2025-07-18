<hr>
<h2 style="margin-top:30px"><?= t('Matriz Eisenhower') ?></h2>

<style>
.eisenhower-container {
    display: grid;
    grid-template-columns: 100px 1fr 1fr;
    grid-template-rows: 30px 1fr 1fr;
    grid-template-areas:
        "corner urgente nourgente"
        "importante do-now schedule"
        "noimportante delegate eliminate";
    gap: 10px;
    margin-top: 20px;
}

.corner {
    grid-area: corner;
}

.urgente {
    grid-area: urgente;
    text-align: center;
    font-weight: bold;
    font-size: 1.1em;
}

.nourgente {
    grid-area: nourgente;
    text-align: center;
    font-weight: bold;
    font-size: 1.1em;
}

.importante {
    grid-area: importante;
    writing-mode: vertical-rl;
    text-align: center;
    font-weight: bold;
    font-size: 1.1em;
    padding-top: 20px;
}

.noimportante {
    grid-area: noimportante;
    writing-mode: vertical-rl;
    text-align: center;
    font-weight: bold;
    font-size: 1.1em;
    padding-top: 20px;
}

.eisenhower-quadrant {
    border: 1px solid #ccc;
    padding: 10px;
    min-height: 200px;
    background: #f9f9f9;
    overflow-y: auto;
}

.eisenhower-quadrant h4 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.1em;
    font-weight: normal;
    color: #444;
}

.task-card {
    background: #fff;
    border: 1px solid #ddd;
    padding: 5px 10px;
    margin-bottom: 5px;
    border-radius: 4px;
    cursor: grab;
}
</style>

<div class="eisenhower-container">

    <div class="corner"></div>

    <div class="urgente"><?= t('Urgente') ?></div>
    <div class="nourgente"><?= t('No urgente') ?></div>

    <div class="importante"><?= t('Importante') ?></div>

    <div class="eisenhower-quadrant" id="do-now"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 3)"
         style="grid-area: do-now;">
        <h4><?= t('Hacer ahora') ?></h4>
        <?php foreach ($tasks_by_priority[3] as $task): ?>
            <div class="task-card"
                 draggable="true"
                 ondragstart="onDragStart(event)"
                 data-task-id="<?= $task['id'] ?>">
                <strong><?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', [
                    'task_id' => $task['id'], 'project_id' => $project['id']
                ]) ?></strong>
            </div>
        <?php endforeach ?>
    </div>

    <div class="eisenhower-quadrant" id="schedule"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 2)"
         style="grid-area: schedule;">
        <h4><?= t('Planificar') ?></h4>
        <?php foreach ($tasks_by_priority[2] as $task): ?>
            <div class="task-card"
                 draggable="true"
                 ondragstart="onDragStart(event)"
                 data-task-id="<?= $task['id'] ?>">
                <strong><?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', [
                    'task_id' => $task['id'], 'project_id' => $project['id']
                ]) ?></strong>
            </div>
        <?php endforeach ?>
    </div>

    <div class="noimportante"><?= t('No importante') ?></div>

    <div class="eisenhower-quadrant" id="delegate"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 1)"
         style="grid-area: delegate;">
        <h4><?= t('Delegar') ?></h4>
        <?php foreach ($tasks_by_priority[1] as $task): ?>
            <div class="task-card"
                 draggable="true"
                 ondragstart="onDragStart(event)"
                 data-task-id="<?= $task['id'] ?>">
                <strong><?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', [
                    'task_id' => $task['id'], 'project_id' => $project['id']
                ]) ?></strong>
            </div>
        <?php endforeach ?>
    </div>

    <div class="eisenhower-quadrant" id="eliminate"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 0)"
         style="grid-area: eliminate;">
        <h4><?= t('Eliminar') ?></h4>
        <?php foreach ($tasks_by_priority[0] as $task): ?>
            <div class="task-card"
                 draggable="true"
                 ondragstart="onDragStart(event)"
                 data-task-id="<?= $task['id'] ?>">
                <strong><?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', [
                    'task_id' => $task['id'], 'project_id' => $project['id']
                ]) ?></strong>
            </div>
        <?php endforeach ?>
    </div>

</div>

<script>
function onDragStart(event) {
    event.dataTransfer.setData("text/plain", event.target.dataset.taskId);
}

function onDragOver(event) {
    event.preventDefault();
}

function onDrop(event, newPriority) {
    event.preventDefault();
    const taskId = event.dataTransfer.getData("text/plain");

    fetch("<?= $this->url->href('BacklogBoardController', 'updatePriority', ['plugin' => 'eisenhower']) ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": "<?= $this->app->config('csrf_token') ?>"
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
</script>
