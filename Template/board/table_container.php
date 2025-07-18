<style>
.eisenhower-board {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    gap: 1rem;
}
.eisenhower-quadrant {
    border: 1px solid #ccc;
    padding: 10px;
    min-height: 200px;
}
.eisenhower-quadrant h3 {
    margin-top: 0;
    font-size: 1.2em;
}
</style>

<div class="eisenhower-board">

    <div class="eisenhower-quadrant" id="do-now">
        <h3><?= t('Urgente e Importante') ?> (Hacer ahora)</h3>
        <?= $this->render('eisenhower:board/tasks', ['tasks' => $tasks_by_priority[3], 'project' => $project]) ?>
    </div>

    <div class="eisenhower-quadrant" id="schedule">
        <h3><?= t('No urgente pero Importante') ?> (Planificar)</h3>
        <?= $this->render('eisenhower:board/tasks', ['tasks' => $tasks_by_priority[2], 'project' => $project]) ?>
    </div>

    <div class="eisenhower-quadrant" id="delegate">
        <h3><?= t('Urgente pero No importante') ?> (Delegar)</h3>
        <?= $this->render('eisenhower:board/tasks', ['tasks' => $tasks_by_priority[1], 'project' => $project]) ?>
    </div>

    <div class="eisenhower-quadrant" id="eliminate">
        <h3><?= t('No urgente ni Importante') ?> (Eliminar)</h3>
        <?= $this->render('eisenhower:board/tasks', ['tasks' => $tasks_by_priority[0], 'project' => $project]) ?>
    </div>

</div>
