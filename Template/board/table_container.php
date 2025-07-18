<?php
$tasks = $this->task->taskFinderModel->getAll($project['id']);

$tasks_by_priority = [
    0 => [],
    1 => [],
    2 => [],
    3 => []
];

foreach ($tasks as $task) {
    $priority = isset($task['priority']) ? (int) $task['priority'] : 0;
    if (!array_key_exists($priority, $tasks_by_priority)) {
        $priority = 0;
    }
    $tasks_by_priority[$priority][] = $task;
}
?>

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
    user-select: none;
}

.nourgente {
    grid-area: nourgente;
    text-align: center;
    font-weight: bold;
    font-size: 1.1em;
    user-select: none;
}

.importante {
    grid-area: importante;
    writing-mode: vertical-rl;
    text-align: center;
    font-weight: bold;
    font-size: 1.1em;
    padding-top: 20px;
    user-select: none;
}

.noimportante {
    grid-area: noimportante;
    writing-mode: vertical-rl;
    text-align: center;
    font-weight: bold;
    font-size: 1.1em;
    padding-top: 20px;
    user-select: none;
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
    user-select: none;
}

.task-card {
    background: #fff;
    border: 1px solid #ddd;
    padding: 5px 10px;
    margin-bottom: 5px;
    border-radius: 4px;
}

.task-card a {
    display: inline-block;
    width: 100%;
    color: #333;
    text-decoration: none;
    font-weight: bold;
}

.task-card small {
    color: #666;
    font-style: italic;
    display: block;
    margin-top: 3px;
}
</style>

<div class="eisenhower-container">

    <div class="corner"></div>
    <div class="urgente"><?= t('Urgente') ?></div>
    <div class="nourgente"><?= t('No urgente') ?></div>
    <div class="importante"><?= t('Importante') ?></div>

    <?php
    $quadrants = [
        3 => ['id' => 'do-now', 'title' => t('Hacer ahora')],
        2 => ['id' => 'schedule', 'title' => t('Planificar')],
        1 => ['id' => 'delegate', 'title' => t('Delegar')],
        0 => ['id' => 'eliminate', 'title' => t('Eliminar')],
    ];

    foreach ($quadrants as $priority => $info): ?>
        <div class="eisenhower-quadrant"
             id="<?= $info['id'] ?>"
             ondragover="onDragOver(event)"
             ondrop="onDrop(event, <?= $priority ?>)"
             style="grid-area: <?= $info['id'] ?>;">
            <h4><?= $info['title'] ?></h4>

            <?php foreach ($tasks_by_priority[$priority] as $task): ?>
                <div class="task-card"
                     data-task-id="<?= $task['id'] ?>">
                    <a href="<?= $this->url->href('TaskViewController', 'show', ['task_id' => $task['id'], 'project_id' => $project['id']]) ?>"
                       draggable="true"
                       ondragstart="onDragStart(event)"
                       data-task-id="<?= $task['id'] ?>">
                        <?= $this->text->e($task['title']) ?>
                    </a>
                    <?php if (!empty($task['assignee_name'])): ?>
                        <small><?= t('Asignado a') ?>: <?= $this->text->e($task['assignee_name']) ?></small>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>

    <div class="noimportante"><?= t('No importante') ?></div>
</div>

<script>
function onDragStart(event) {
    const taskId = event.target.dataset.taskId;
    if (taskId) {
        event.dataTransfer.setData("text/plain", taskId);
    }
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
