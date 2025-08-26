<?php

namespace Kanboard\Plugin\Eisenhower;

use DateTime;
use Kanboard\Core\Plugin\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Plugin\Eisenhower\Model\ProjectUsesBacklogBoardModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TaskPositionModel;
use Kanboard\Core\Translator;
use Kanboard\Core\Security\Role;

class Plugin extends Base
{
    public function initialize()
    {

        // REGISTRO DEL MODEL EN EL CONTAINER
        //$this->container['ProjectUsesBacklogBoardModel'] = function ($c) {
        //return new \Kanboard\Plugin\Eisenhower\Model\ProjectUsesBacklogBoardModel($c);
        //};
        
        $this->template->setTemplateOverride('board/table_container','eisenhower:board/table_container');
        //$this->template->setTemplateOverride('column/index','eisenhower:column/index');
        $this->template->setTemplateOverride('swimlane/table','eisenhower:swimlane/table');
        $this->hook->on('template:layout:js', array('template' => 'plugins/Eisenhower/Assets/backlog.js'));
        $this->hook->on('template:layout:css', array('template' => 'plugins/Eisenhower/Assets/backlog.css'));
        //$this->template->hook->attach('template:project:dropdown', 'eisenhower:board/menu');
        $this->route->addRoute('/eisenhower/updatePriority', 'BacklogBoardController', 'updatePriority', 'Eisenhower');

        
        //CONFIG HOOK
        //$this->template->hook->attach('template:config:board', 'backlog:config/board_name');    
        
        //$projects = $this->projectModel->getAllByStatus(1); 
        //foreach ($projects as $project) {
        //    if ($this->projectUsesBacklogBoardModel->backlogIsset($project['id'])) {
        //       $columnId = $this->columnModel->getColumnIdByTitle($project['id'], 'Backlog_Board');
        //       $tasksInColumn = $this->projectUsesBacklogBoardModel->getTasksInColumn($project['id'], $columnId);
        //       foreach($tasksInColumn as $task) {
        //             $swimlane = $this->swimlaneModel->getById($task['swimlane_id']);
        //             if ($swimlane['position'] !== 1) {
        //                 $this->taskPositionModel->movePosition($project['id'], $task['id'], $columnId , 1, $this->swimlaneModel->getByName($project['id'], "Backlog_swimlane")['id'], true, false); 
        //             }
        //        }
        //    }
        //}

         // Hook seguro: mover tareas después de que Kanboard haya cargado los servicios
        $this->hook->on('template:layout:begin', function() {
            $projects = $this->projectModel->getAllByStatus(1);
            foreach ($projects as $project) {
                if ($this->container['ProjectUsesBacklogBoardModel']->backlogIsset($project['id'])) {
                    $columnId = $this->columnModel->getColumnIdByTitle($project['id'], 'Backlog_Board');
                    $tasksInColumn = $this->container['ProjectUsesBacklogBoardModel']->getTasksInColumn($project['id'], $columnId);
                    foreach ($tasksInColumn as $task) {
                        $swimlane = $this->swimlaneModel->getById($task['swimlane_id']);
                        if ($swimlane['position'] !== 1) {
                            $this->taskPositionModel->movePosition(
                                $project['id'],
                                $task['id'],
                                $columnId,
                                1,
                                $this->swimlaneModel->getByName($project['id'], "Backlog_swimlane")['id'],
                                true,
                                false
                            );
                        }
                    }
                }
            }
        });
    }  

    public function getClasses()
{
    return [
        'Plugin\Eisenhower\Model' => [
            'ProjectUsesBacklogBoardModel',
        ],
        'Plugin\Eisenhower\Controller' => [
            'BacklogBoardController',
        ],
    ];
}

    
    public function getPluginName()
    {
        return 'Eisenhower';
    }
    
    public function getPluginDescription()
    {
        return t('Plugin para mostrar cuadrante Eisenhower en proyectos');
    }
    
    public function getPluginAuthor()
    {
        return 'Andrés Silva';
    }
    
    public function getPluginVersion()
    {
        return '1.0.5';
    }
    
    public function getPluginHomepage()
    {
        return '...';
    }
    
    public function getCompatibleVersion()
    {
        return '>=1.0.45';
    }
}