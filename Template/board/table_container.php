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

/* Contenedor de los títulos principales, 2 columnas */
.eisenhower-titles {
    grid-column: 1 / span 2;
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-weight: bold;
    font-size: 1.3em;
}

.eisenhower-quadrant {
    border: 1px solid #ccc;
    padding: 10px;
    min-height: 200px;
    background: #f9f9f9;
    position: relative;
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

<div class="eisenhower-board">

    <!-- Títulos principales arriba, que explican el cuadrante -->
    <div class="eisenhower-titles">
        <div><?= t('Urgente') ?></div>
        <div><?= t('No urgente') ?></div>
    </div>

    <!-- Cuadrantes -->

    <div class="eisenhower-quadrant" id="do-now"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 3)">
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
         ondrop="onDrop(event, 2)">
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

    <div class="eisenhower-quadrant" id="delegate"
         ondragover="onDragOver(event)"
         ondrop="onDrop(event, 1)">
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
         ondrop="onDrop(event, 0)">
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

    <!-- Títulos verticales a la izquierda -->
    <div class="eisenhower-titles" style="grid-column: 1 / span 1; grid-row: 1 / span 2; flex-direction: column; justify-content: space-between; font-weight: bold; font-size: 1.3em; margin-top: 1rem;">
        <div><?= t('Importante') ?></div>
        <div><?= t('No importante') ?></div>
    </div>

</div>
