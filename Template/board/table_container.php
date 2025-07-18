<?php
$tasks = $this->task->taskFinderModel->getAll($project['id']);

$tasks_by_priority = [
    0 => [],
    1 => [],
    2 => [],
    3 => []
];

foreach ($tasks as $task) {
    $priority = (int) $task['priority'];
    $tasks_by_priority[$priority][] = $task;
}
?>

<hr>
<h2 style="margin-top:30px"><?= t('Matriz Eisenhower') ?></h2>

<style>
.eisenhower-board {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    gap: 1rem;
    margin-top: 20px;
}
.eisenhower-quadrant {
    border: 1px solid #ccc;
    padding: 10px;
    min-height: 200px;
    background: #f9f9f9;
}
.eisenhower-quadrant h3 {
    margin-top: 0;
    font-size: 1.2em;
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

<div class="eisenhower-board">

    <div class="eisenhower-quadrant" id="do-now"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 3)">
        <h3><?= t('Urgente e Importante') ?> (Hacer ahora)</h3>
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
         ondrop="onDrop(event, 2)">
        <h3><?= t('No urgente pero Importante') ?> (Planificar)</h3>
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

    <div class="eisenhower-quadrant" id="delegate"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 1)">
        <h3><?= t('Urgente pero No importante') ?> (Delegar)</h3>
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
         ondrop="onDrop(event, 0)">
        <h3><?= t('No urgente ni Importante') ?> (Eliminar)</h3>
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

    fetch("<?= $this->url->href('EisenhowerPriorityController', 'updatePriority', ['plugin' => 'eisenhower']) ?>", {
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
