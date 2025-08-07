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

<style>
.eisenhower-container {
    display: grid;
    grid-template-columns: 35px 1fr 1fr;
    grid-template-rows: 30px 1fr 1fr;
    grid-template-areas:
        "corner urgente nourgente"
        "importante do-now schedule"
        "noimportante delegate eliminate";
    gap: 10px;
    margin-top: 10px;
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
    cursor: grab;
}

.task-card small {
    color: #666;
    font-style: italic;
    display: block;
    margin-top: 3px;
}
</style>

<!-- FORMULARIO NUEVA TAREA -->
    <a href="/?controller=TaskCreationController&amp;action=show&amp;project_id=1" class="js-modal-large" title=""><i class="fa fa-plus fa-fw js-modal-large" aria-hidden="true"></i>Añadir una nueva tarea</a>


<div class="eisenhower-container">

    <div class="corner"></div>
    <div class="urgente"><?= t('Urgente') ?></div>
    <div class="nourgente"><?= t('No urgente') ?></div>
    <div class="importante"><?= t('Importante') ?></div>

    <?php
    $quadrants = [
        3 => ['id' => 'do-now', 'title' => t('Hacer ahora (3)')],
        2 => ['id' => 'schedule', 'title' => t('Planificar (2)')],
        1 => ['id' => 'delegate', 'title' => t('Delegar (1)')],
        0 => ['id' => 'eliminate', 'title' => t('Eliminar (0)')],
    ];

    foreach ($quadrants as $priority => $info): ?>
    <div class="eisenhower-quadrant"
         id="<?= $info['id'] ?>"
         style="grid-area: <?= $info['id'] ?>;"
         data-priority="<?= $priority ?>">

        <h4><?= $info['title'] ?></h4>

        <?php foreach ($tasks_by_priority[$priority] as $task): ?>
            <div class="task-card" draggable="true" data-task-id="<?= $task['id'] ?>">
    <div class="task-card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <?= $this->url->link(
            '<strong>' . $this->text->e($task['title']) . '</strong>',
            'TaskViewController',
            'show',
            ['task_id' => $task['id'], 'project_id' => $project['id']],
            false
        ) ?>

        <a href="<?= $this->url->href('TaskModificationController', 'edit', ['task_id' => $task['id']]) ?>"
           class="js-modal-large"
           title="<?= t('Editar tarea') ?>">
            <i class="fa fa-edit fa-fw" aria-hidden="true"></i> <?= t('Editar') ?>
        </a>

        <select name="priority" id="form-priority" class="" tabindex="9"><option value="0">0</option><option value="1">1</option><option value="2" selected="selected">2</option><option value="3">3</option></select>
    </div>

    <?php if (!empty($task['assignee_name'])): ?>
        <small><?= t('Asignado a') ?>: <?= $this->text->e($task['assignee_name']) ?></small>
    <?php endif ?>
</div>

        <?php endforeach ?>
    </div>
<?php endforeach ?>

    <div class="noimportante"><?= t('No importante') ?></div>
</div>

<div id="eisenhower-config"
     data-csrf-token="<?= $this->app->config('csrf_token') ?>"
     data-update-priority-url="<?= $this->url->href('BacklogBoardController', 'updatePriority', ['plugin' => 'eisenhower']) ?>">
</div>

<script src="<?= $this->url->dir() ?>plugins/Eisenhower/Assets/backlog.js"></script>