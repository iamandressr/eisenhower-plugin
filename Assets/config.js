window.eisenhowerConfig = {
    csrfToken: "<?= $this->app->config('csrf_token') ?>",
    updatePriorityUrl: "<?= $this->url->href('BacklogBoardController', 'updatePriority', ['plugin' => 'eisenhower']) ?>"
};
