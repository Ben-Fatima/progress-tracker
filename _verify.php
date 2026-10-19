<?php
// Verify dashboard
$dashboard = @file_get_contents('http://127.0.0.1:8766');
if ($dashboard === false) { echo "FAIL: Dashboard not loading\n"; exit(1); }
echo (strpos($dashboard, 'View all') !== false ? 'OK' : 'MISS') . ": Dashboard has categories summary link\n";
echo (strpos($dashboard, 'Categories') !== false ? 'OK' : 'MISS') . ": Dashboard shows Categories\n";
echo (strpos($dashboard, 'showCreateForm') === false ? 'OK' : 'FAIL') . ": Dashboard has no create form\n";
echo (strpos($dashboard, 'editingCategoryId') === false ? 'OK' : 'FAIL') . ": Dashboard has no edit form\n";

// Verify categories page
$cats = @file_get_contents('http://127.0.0.1:8766/categories');
if ($cats === false) { echo "FAIL: Categories page not loading\n"; exit(1); }
echo (strpos($cats, 'New Category') !== false ? 'OK' : 'MISS') . ": Categories page has New Category button\n";
echo (strpos($cats, 'Relocation NL/DE') !== false ? 'OK' : 'MISS') . ": Categories page has Relocation\n";
echo (strpos($cats, 'Fikrify') !== false ? 'OK' : 'MISS') . ": Categories page has Fikrify\n";
echo (strpos($cats, 'Interview Preparation') !== false ? 'OK' : 'MISS') . ": Categories page has Interview Prep\n";
echo (strpos($cats, 'Manage your projects') !== false ? 'OK' : 'MISS') . ": Categories page has subtitle\n";

// Check sidebar has categories link
echo (strpos($dashboard, 'route(\'categories\')') !== false || strpos($dashboard, '/categories') !== false ? 'OK' : 'MISS') . ": Sidebar has categories link\n";
