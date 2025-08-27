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

<div style="display: flex; gap: 10px; margin-bottom: 15px;">
    <a href="/?controller=TaskCreationController&amp;action=show&amp;project_id=1" class="js-modal-large" title=""><i class="fa fa-plus fa-fw js-modal-large" aria-hidden="true"></i>Añadir una nueva tarea</a>

    <p>|</p>

    <a href="/?controller=ExternalTaskCreationController&amp;action=step1&amp;project_id=1&amp;swimlane_id=2&amp;column_id=2&amp;provider_name=Mantis" class="js-modal-large btn-add-mantis" title="">
        <i class="fa fa-bug fa-fw" aria-hidden="true"></i> Añadir Tarea de Mantis
    </a>
</div>

<div class="eisenhower-container">

    <div class="corner"></div>
    <div class="urgente"><?= t('Urgente') ?></div>
    <div class="nourgente"><?= t('No urgente') ?></div>
    <div class="importante"><?= t('Importante') ?></div>

    <?php
    $colors = [
        3 => '#ffb3b3', // Hacer ahora
        2 => '#ffd9b3', // Planificar
        1 => '#ffffb3', // Delegar
        0 => '#b3ffb3'  // Eliminar
    ];

    $quadrants = [
        3 => ['id' => 'do-now', 'title' => t('Hacer ahora (3)')],
        2 => ['id' => 'schedule', 'title' => t('Planificar (2)')],
        1 => ['id' => 'delegate', 'title' => t('Delegar (1)')],
        0 => ['id' => 'eliminate', 'title' => t('Eliminar (0)')],
    ];

    foreach ($quadrants as $priority => $info): ?>
    <div class="eisenhower-quadrant"
         id="<?= $info['id'] ?>"
         style="grid-area: <?= $info['id'] ?>;
         background-color: <?= $colors[$priority] ?>;"
         data-priority="<?= $priority ?>">

        <h4><?= $info['title'] ?></h4>

        <?php foreach ($tasks_by_priority[$priority] as $task): ?>       
            <div class="task-card" draggable="true"
                 data-task-id="<?= $task['id'] ?>"
                 data-column-id="<?= $task['column_id'] ?>"
                 data-swimlane-id="<?= $task['swimlane_id'] ?>"
                 data-position="<?= $task['position'] ?>"
            >
                <div class="task-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <?= $this->url->link(
                        '<strong>' . $this->text->e($task['title']) . '</strong>',
                        'TaskViewController',
                        'show',
                        ['task_id' => $task['id'], 'project_id' => $project['id']],
                        false
                    ) ?>

                    <div style="display: flex; gap: 10px;">
                        <a href="<?= $this->url->href('TaskModificationController', 'edit', ['task_id' => $task['id']]) ?>"
                           class="js-modal-large"
                           title="<?= t('Editar tarea') ?>">
                            <i class="fa fa-edit fa-fw" aria-hidden="true"></i> <?= t('Editar') ?>
                        </a>

                        <p> | </p>

                        <a href="<?= $this->url->href('TaskSuppressionController', 'confirm', ['task_id' => $task['id'], 'redirect' => 'board']) ?>"
                           class="js-modal-confirm"
                           title="<?= t('Suprimir tarea') ?>">
                            <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> <?= t('Eliminar') ?>
                        </a>
                    </div>
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
     data-csrf-token="<?= $this->app->csrfToken() ?>"
     data-update-priority-url="<?= $this->url->href('BacklogBoardController', 'updatePriority', ['plugin' => 'Eisenhower'], false, '', true) ?>"
     data-move-task-url="<?= $this->url->href('BacklogBoardController', 'moveTask', ['plugin' => 'Eisenhower'], false, '', true) ?>">


</div>

<script src="<?= $this->url->dir() ?>plugins/Eisenhower/Assets/backlog.js"></script>
<link rel="stylesheet" href="<?= $this->url->dir() ?>plugins/Eisenhower/Assets/backlog.css">
