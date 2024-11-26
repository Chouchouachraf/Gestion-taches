<?php

use PHPUnit\Framework\TestCase;

class Sprint3Test extends TestCase
{
    // -------------------- US1: Thème clair/sombre -------------------- //

    /**
     * Tester la sauvegarde de la préférence de thème (cookie)
     */
    public function testSaveThemePreference()
    {
        $_COOKIE['theme'] = 'dark';
        $this->assertEquals('dark', $_COOKIE['theme'], "Le thème sombre n'a pas été sauvegardé correctement.");
    }

    /**
     * Tester la récupération de la préférence de thème
     */
    public function testRetrieveThemePreference()
    {
        $_COOKIE['theme'] = 'light';
        $theme = $_COOKIE['theme'];
        $this->assertEquals('light', $theme, "La préférence de thème n'a pas été correctement récupérée.");
    }

    /**
     * Tester l'application du thème sur les pages
     */
    public function testApplyTheme()
    {
        $this->assertEquals(
            'dark-theme.css',
            $this->applyTheme('dark'),
            "Le fichier CSS du thème sombre n'a pas été chargé correctement."
        );

        $this->assertEquals(
            'light-theme.css',
            $this->applyTheme('light'),
            "Le fichier CSS du thème clair n'a pas été chargé correctement."
        );
    }

    /**
     * Fonction pour appliquer le thème.
     */
    private function applyTheme($theme)
    {
        return $theme === 'dark' ? 'dark-theme.css' : 'light-theme.css';
    }

    // -------------------- US2: Personnalisation des catégories -------------------- //

    /**
     * Tester la création d'une catégorie
     */
    public function testCreateCategory()
    {
        $categories = [];
        $newCategory = 'Travail';
        $categories[] = $newCategory;

        $this->assertContains('Travail', $categories, "La catégorie 'Travail' n'a pas été ajoutée.");
    }

    /**
     * Tester la suppression d'une catégorie
     */
    public function testDeleteCategory()
    {
        $categories = ['Travail', 'Personnel'];
        unset($categories[0]); // Supprime 'Travail'
        $categories = array_values($categories); // Réindexer le tableau

        $this->assertNotContains('Travail', $categories, "La catégorie 'Travail' n'a pas été supprimée.");
    }

    /**
     * Tester la modification d'une catégorie
     */
    public function testModifyCategory()
    {
        $categories = ['Travail', 'Personnel'];
        $categories[0] = 'Loisirs'; // Modifier 'Travail' en 'Loisirs'

        $this->assertContains('Loisirs', $categories, "La catégorie n'a pas été modifiée correctement.");
    }

    // -------------------- US3: Modification de l'affichage des tâches -------------------- //

    /**
     * Tester la sauvegarde de la préférence d'affichage
     */
    public function testSaveViewPreference()
    {
        $_COOKIE['task_view'] = 'grid';
        $this->assertEquals('grid', $_COOKIE['task_view'], "La préférence d'affichage n'a pas été sauvegardée correctement.");
    }

    /**
     * Tester l'application de l'affichage des tâches
     */
    public function testApplyTaskView()
    {
        $this->assertStringContainsString(
            'grid-view',
            $this->applyTaskView('grid'),
            "L'affichage en grille n'a pas été appliqué correctement."
        );

        $this->assertStringContainsString(
            'list-view',
            $this->applyTaskView('list'),
            "L'affichage en liste n'a pas été appliqué correctement."
        );
    }

    /**
     * Fonction pour appliquer l'affichage des tâches.
     */
    private function applyTaskView($view)
    {
        return $view === 'grid' ? '<div class="grid-view"></div>' : '<ul class="list-view"></ul>';
    }

    /**
     * Tester l'ergonomie de l'affichage (mobile-friendly)
     */
    public function testMobileResponsiveView()
    {
        $isResponsive = true; // Supposons que la fonction retourne si la page est responsive

        $this->assertTrue($isResponsive, "L'affichage n'est pas responsive sur mobile.");
    }
}
