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

// Mapea prioridad a columna y swimlane del Eisenhower Board
$quadrant_mapping = [
    3 => ['column_id' => $do_now_column_id, 'swimlane_id' => $do_now_swimlane_id],
    2 => ['column_id' => $schedule_column_id, 'swimlane_id' => $schedule_swimlane_id],
    1 => ['column_id' => $delegate_column_id, 'swimlane_id' => $delegate_swimlane_id],
    0 => ['column_id' => $eliminate_column_id, 'swimlane_id' => $eliminate_swimlane_id],
];
?>

<hr>

<div class="eisenhower-container">

    <?php
    $colors = [
        3 => '#ffb3b3',
        2 => '#ffd9b3',
        1 => '#ffffb3',
        0 => '#b3ffb3'
    ];

    $quadrants = [
        3 => ['id' => 'do-now', 'title' => t('Hacer ahora (3)')],
        2 => ['id' => 'schedule', 'title' => t('Planificar (2)')],
        1 => ['id' => 'delegate', 'title' => t('Delegar (1)')],
        0 => ['id' => 'eliminate', 'title' => t('Eliminar (0)')],
    ];

    foreach ($quadrants as $priority => $info):
        $columnId = $quadrant_mapping[$priority]['column_id'];
        $swimlaneId = $quadrant_mapping[$priority]['swimlane_id'];
    ?>
    <div class="eisenhower-quadrant"
         id="<?= $info['id'] ?>"
         style="grid-area: <?= $info['id'] ?>; background-color: <?= $colors[$priority] ?>;"
         data-priority="<?= $priority ?>"
         data-column-id="<?= $columnId ?>"
         data-swimlane-id="<?= $swimlaneId ?>">

        <h4><?= $info['title'] ?></h4>

        <?php foreach ($tasks_by_priority[$priority] as $task): ?>       
            <div class="task-card" draggable="true"
                 data-task-id="<?= $task['id'] ?>"
                 data-column-id="<?= $task['column_id'] ?>"
                 data-swimlane-id="<?= $task['swimlane_id'] ?>"
                 data-position="<?= $task['position'] ?>">
                <!-- tu HTML de task-card aquí -->
            </div>
        <?php endforeach ?>
    </div>
    <?php endforeach ?>
</div>

<div id="eisenhower-config"
     data-csrf-token="<?= $this->app->config('csrf_token') ?>"
     data-update-priority-url="<?= $this->url->href('BacklogBoardController', 'updatePriority', [], 'Eisenhower') ?>"
     data-move-task-url="<?= $this->url->href('BacklogBoardController', 'moveTask', [], 'Eisenhower') ?>">
</div>


<script src="<?= $this->url->dir() ?>plugins/Eisenhower/Assets/backlog.js"></script>
<style src="<?= $this->url->dir() ?>plugins/Eisenhower/Assets/backlog.css></style> 