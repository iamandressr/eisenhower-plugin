<?php

namespace Kanboard\Plugin\Eisenhower\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\TaskPositionModel;


class BacklogBoardController extends BaseController {

/**
 * sets the board by creating swimlane and column
 */
    public function backlogSet() {
        $projectId = $this->request->getIntegerParam('project_id');

        $this->projectUsesBacklogBoardModel->setBacklogBoard($projectId);
        $this->backlogSwimlane($projectId);
        $this->backlogColumn($projectId);

        $this->flash->success(t('Backlog Board now activated.'));

        $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $projectId), true));
    }

/**
 * unsets the board by moving tasks out of created column/swimlane to next column/swimlane, then removes created column/swimlane
 */
    
    public function backlogUnset() {
        $projectId = $this->request->getIntegerParam('project_id');

        $this->projectUsesBacklogBoardModel->unsetBacklogBoard($projectId);
        $this->moveTasksOut($projectId);
        $this->removeBacklogSwimlane($projectId);
        $this->removeBacklogColumn($projectId);

        $this->flash->success(t('Backlog Board now deactivated.'));

        $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $projectId), true));
    }
    
/**
 * creates the swimlane 'Backlog_Swimlane'
 */
    
    public function backlogSwimlane($projectId) {
          $this->swimlaneModel->create($projectId, 'Backlog_Swimlane', 'Temporary Swimlane for Backlog Board');  
          $this->swimlaneModel->changePosition($projectId, $this->swimlaneModel->getIdByName($projectId, 'Backlog_Swimlane'), 1);
    }
    
/**
 * removes the swimlane 'Backlog_Swimlane'
 */
    
    public function removeBacklogSwimlane($projectId) {
          $this->swimlaneModel->remove($projectId, $this->swimlaneModel->getIdByName($projectId, 'Backlog_Swimlane'));
    }
    
/**
 * creates the column 'Backlog_Board'
 */
    
    public function backlogColumn($projectId) {
          $this->columnModel->create($projectId, 'Backlog_Board', 0, 'Main Column for Backlog Board', 0);
          $this->columnModel->changePosition($projectId, $this->columnModel->getColumnIdByTitle($projectId, 'Backlog_Board'), 1);
    }
    
/**
 * removes the column 'Backlog_Board'
 */
    
    public function removeBacklogColumn($projectId) {
          $this->columnModel->remove($this->columnModel->getColumnIdByTitle($projectId, 'Backlog_Board'));
    }
    
/**
 * moves tasks out of column/swimlane
 */
    
    public function moveTasksOut($projectId) {
          $columnId = $this->columnModel->getColumnIdByTitle($projectId, 'Backlog_Board');
          $allColumns = $this->columnModel->getAll($projectId);
          foreach ($allColumns as $column) { if ($column['position'] == 2) { $column_to = $column['id']; } }
          $allSwimlanes = $this->swimlaneModel->getAll($projectId);
          foreach ($allSwimlanes as $swimlane) { if ($swimlane['position'] == 2) { $swimlane_to = $swimlane['id']; } } 
          $tasksInColumn = $this->projectUsesBacklogBoardModel->getTasksInColumn($projectId, $columnId);
          foreach ($tasksInColumn as $task) { $this->taskPositionModel->movePosition($projectId, $task['id'], $column_to, 1, $swimlane_to, true, false); }
    }

     public function updatePriority()
{
    $data = json_decode($this->request->getBody(), true);

    $task_id = isset($data['task_id']) ? (int) $data['task_id'] : 0;
    $priority = isset($data['priority']) ? (int) $data['priority'] : 0;

    error_log("UpdatePriority called: task_id=$task_id, priority=$priority");

    if ($task_id === 0) {
        return $this->response->json(['status' => 'error', 'message' => 'ID de tarea inválido'], 400);
    }

    $task = $this->taskFinderModel->getById($task_id);
    if (empty($task)) {
        error_log("Task not found: $task_id");
        return $this->response->json(['status' => 'error', 'message' => 'Tarea no encontrada'], 404);
    }

    $updated = $this->taskModificationModel->update([
        'id' => $task_id,
        'priority' => $priority,
    ]);

    if ($updated === false) {
        error_log("Failed to update priority for task $task_id");
        return $this->response->json(['status' => 'error', 'message' => 'Error al actualizar prioridad'], 500);
    } else {
        error_log("Priority updated successfully for task $task_id");
        return $this->response->json(['status' => 'ok']);
    }
}





    public function createTask()
{
    $this->checkCSRFParam();

    $data = json_decode($this->request->getBody(), true);

    $project_id = isset($data['project_id']) ? (int) $data['project_id'] : 0;
    $title = isset($data['title']) ? trim($data['title']) : '';
    $priority = isset($data['priority']) ? (int) $data['priority'] : 0;

    if (empty($title) || $project_id === 0) {
        return $this->response->json(['error' => 'Parámetros inválidos'], 400);
    }

    $values = [
        'project_id' => $project_id,
        'title' => $title,
        'priority' => $priority,
        // Opcional: asignado, fechas, etc.
    ];

    $task_id = $this->taskCreation->create($values);

    if ($task_id === false) {
        return $this->response->json(['error' => 'No se pudo crear la tarea'], 500);
    }

    $task = $this->taskFinderModel->getById($task_id);

    return $this->response->json([
        'id' => $task['id'],
        'title' => $task['title'],
        'assignee_name' => isset($task['assignee_name']) ? $task['assignee_name'] : ''
    ]);
}

public function moveTask()
{
    $this->checkCSRFParam();

    $data = json_decode($this->request->getBody(), true);

    error_log('moveTask called with data: ' . json_encode($data));

    if (empty($data['task_id']) || empty($data['column_id']) || !isset($data['position']) || empty($data['swimlane_id'])) {
        return $this->response->json(['error' => 'Invalid data'], 400);
    }

    $task = $this->taskFinderModel->getById($data['task_id']);

    if (empty($task)) {
        return $this->response->json(['error' => 'Task not found'], 404);
    }

    if (! $this->helper->projectRole->canMoveTask($task['project_id'], $task['column_id'], $data['column_id'])) {
        return $this->response->json(['error' => 'Access denied'], 403);
    }

    $moved = $this->taskPositionModel->movePosition(
        $task['project_id'],
        $task['id'],
        $data['column_id'],
        $data['position'],
        $data['swimlane_id']
    );

    if ($moved === false) {
        return $this->response->json(['error' => 'Failed to move task'], 500);
    }

    return $this->response->json(['success' => true]);
}

}