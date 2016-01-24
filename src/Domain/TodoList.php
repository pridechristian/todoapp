<?php

namespace App\Domain;

use App\Domain\Exception\NotFoundException;
use Doctrine\Common\Collections\ArrayCollection;

class TodoList
{
    private $id;

    private $name;

    private $tasks;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->tasks = new ArrayCollection();
    }

    public function addTask(string $description) : Task
    {
        $task = new Task($this, $description);
        $this->tasks->add($task);

        return $task;
    }

    public function markTaskDone(string $id)
    {
        $task = $this->getTaskByID($id);
        $task->done();
    }

    public function removeTask(string $id)
    {
        $task = $this->getTaskByID($id);
        $this->tasks->removeElement($task);
    }

    public function countTasks() : int
    {
        return $this->tasks->count();
    }

    public function countPendingTasks() : int
    {
        return $this->tasks->matching(Task::pendingCriteria())->count();
    }

    public function toArrayWithTasks() : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tasks' => array_map(function (Task $task) {

                return $task->toArray();
            }, $this->tasks->toArray())
        ];
    }

    private function getTaskByID($id) : Task
    {
        $task = $this->tasks->matching(Task::byIdCriteria($id))->first();
        if (false === $task) {
            throw new NotFoundException(sprintf('Task with id "%d" not found in todo list', $id));
        }

        return $task;
    }

}
