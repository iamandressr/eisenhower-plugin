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
        
        $this->template->setTemplateOverride('board/table_container','eisenhower:board/table_container');
        $this->template->setTemplateOverride('column/index','eisenhower:column/index');
        $this->template->setTemplateOverride('swimlane/table','eisenhower:swimlane/table');
        $this->hook->on('template:layout:js', array('template' => 'plugins/Eisenhower/Assets/backlog.js'));
        $this->hook->on('template:layout:css', array('template' => 'plugins/Eisenhower/Assets/backlog.css'));
        $this->template->hook->attach('template:project:dropdown', 'eisenhower:board/menu');
        
        //CONFIG HOOK
        $this->template->hook->attach('template:config:board', 'backlog:config/board_name');    
        
        $projects = $this->projectModel->getAllByStatus(1); //get all projects that are active
        foreach ($projects as $project) {
            if ($this->projectUsesBacklogBoardModel->backlogIsset($project['id'])) {
               $columnId = $this->columnModel->getColumnIdByTitle($project['id'], 'Backlog_Board');
               $tasksInColumn = $this->projectUsesBacklogBoardModel->getTasksInColumn($project['id'], $columnId);
               foreach($tasksInColumn as $task) {
                     $swimlane = $this->swimlaneModel->getById($task['swimlane_id']);
                     if ($swimlane['position'] !== 1) {
                         $this->taskPositionModel->movePosition($project['id'], $task['id'], $columnId , 1, $this->swimlaneModel->getByName($project['id'], "Backlog_swimlane")['id'], true, false); 
                     }
                }
            }
        }
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getClasses() {
        return array(
            'Plugin\Eisenhower\Model' => array(
                'ProjectUsesBacklogBoardModel',
            )
        );
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
