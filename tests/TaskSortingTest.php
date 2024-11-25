<?php
use PHPUnit\Framework\TestCase;

class TaskSortingTest extends TestCase
{
    private $tasks;

    protected function setUp(): void
    {
        // Simuler une liste de tâches avec des priorités et des dates limites
        $this->tasks = [
            ['title' => 'Task A', 'priority' => 'low', 'due_date' => '2024-12-05'],
            ['title' => 'Task B', 'priority' => 'medium', 'due_date' => '2024-12-02'],
            ['title' => 'Task C', 'priority' => 'high', 'due_date' => '2024-12-10'],
            ['title' => 'Task D', 'priority' => 'medium', 'due_date' => '2024-12-01'],
        ];
    }

    public function testSortByPriority()
    {
        // Appeler une fonction qui effectue le tri par priorité
        $sortedTasks = $this->sortTasksByPriority($this->tasks);

        // Vérifier que les priorités sont dans l'ordre attendu
        $this->assertEquals('high', $sortedTasks[0]['priority']);
        $this->assertEquals('medium', $sortedTasks[1]['priority']);
        $this->assertEquals('medium', $sortedTasks[2]['priority']);
        $this->assertEquals('low', $sortedTasks[3]['priority']);
    }

    public function testSortByDueDate()
    {
        // Appeler une fonction qui effectue le tri par date limite
        $sortedTasks = $this->sortTasksByDueDate($this->tasks);

        // Vérifier que les dates limites sont dans l'ordre attendu
        $this->assertEquals('2024-12-01', $sortedTasks[0]['due_date']);
        $this->assertEquals('2024-12-02', $sortedTasks[1]['due_date']);
        $this->assertEquals('2024-12-05', $sortedTasks[2]['due_date']);
        $this->assertEquals('2024-12-10', $sortedTasks[3]['due_date']);
    }

    // Simuler la fonction pour trier par priorité
    private function sortTasksByPriority(array $tasks)
    {
        usort($tasks, function ($a, $b) {
            $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
            return $priorityOrder[$a['priority']] <=> $priorityOrder[$b['priority']];
        });
        return $tasks;
    }

    // Simuler la fonction pour trier par date limite
    private function sortTasksByDueDate(array $tasks)
    {
        usort($tasks, function ($a, $b) {
            return strtotime($a['due_date']) <=> strtotime($b['due_date']);
        });
        return $tasks;
    }
}
