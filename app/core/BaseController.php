<?php
class BaseController
{
    protected function render($view, $data = [])
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            echo "View not found: " . htmlspecialchars($view);
            return;
        }
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require $viewFile;
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
