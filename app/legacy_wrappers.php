<?php

// Employee action wrappers
if (!function_exists('handleEmployeeLogin')) {
    function handleEmployeeLogin() { return (new EmployeeController())->handleEmployeeLogin(); }
}
if (!function_exists('handleEmployeeLogout')) {
    function handleEmployeeLogout() { return (new EmployeeController())->handleEmployeeLogout(); }
}
if (!function_exists('handleProductSave')) {
    function handleProductSave() { return (new EmployeeController())->handleProductSave(); }
}
if (!function_exists('handleProductUpdate')) {
    function handleProductUpdate() { return (new EmployeeController())->handleProductUpdate(); }
}
if (!function_exists('handleProductDelete')) {
    function handleProductDelete() { return (new EmployeeController())->handleProductDelete(); }
}
if (!function_exists('handleOrderStatusUpdate')) {
    function handleOrderStatusUpdate() { return (new EmployeeController())->handleOrderStatusUpdate(); }
}
if (!function_exists('handleOrderCreate')) {
    function handleOrderCreate() { return (new EmployeeController())->handleOrderCreate(); }
}
if (!function_exists('handleGetCustomerAddresses')) {
    function handleGetCustomerAddresses() { return (new EmployeeController())->handleGetCustomerAddresses(); }
}
if (!function_exists('handleOrderCreateAjax')) {
    function handleOrderCreateAjax() { return (new EmployeeController())->handleOrderCreateAjax(); }
}
if (!function_exists('handleEmployeeSave')) {
    function handleEmployeeSave() { return (new EmployeeController())->handleEmployeeSave(); }
}
if (!function_exists('handleEmployeeUpdate')) {
    function handleEmployeeUpdate() { return (new EmployeeController())->handleEmployeeUpdate(); }
}
if (!function_exists('handleEmployeeDelete')) {
    function handleEmployeeDelete() { return (new EmployeeController())->handleEmployeeDelete(); }
}
if (!function_exists('handleEmployeeProfileUpdate')) {
    function handleEmployeeProfileUpdate() { return (new EmployeeController())->handleEmployeeProfileUpdate(); }
}
if (!function_exists('routeEmployeeActions')) {
    function routeEmployeeActions($page, $action = '') { return (new EmployeeController())->routeEmployeeActions($page, $action); }
}

// Employee auth wrappers
if (!function_exists('employeeLogin')) {
    function employeeLogin() { return EmployeeAuthController::employeeLogin(); }
}
if (!function_exists('employeeLogout')) {
    function employeeLogout(): void { EmployeeAuthController::employeeLogout(); }
}
if (!function_exists('requireEmployeeLogin')) {
    function requireEmployeeLogin(): void { EmployeeAuthController::requireEmployeeLogin(); }
}
if (!function_exists('requireEmployeeOrManager')) {
    function requireEmployeeOrManager(): void { EmployeeAuthController::requireEmployeeOrManager(); }
}
if (!function_exists('requireEmployeeAdmin')) {
    function requireEmployeeAdmin(): void { EmployeeAuthController::requireEmployeeAdmin(); }
}

// Admin aliases
if (!function_exists('adminLogin')) {
    function adminLogin() { return employeeLogin(); }
}
if (!function_exists('adminLogout')) {
    function adminLogout() { return employeeLogout(); }
}
if (!function_exists('requireAdminLogin')) {
    function requireAdminLogin() { return requireEmployeeLogin(); }
}
if (!function_exists('requireAdminOrManager')) {
    function requireAdminOrManager() { requireEmployeeOrManager(); }
}
if (!function_exists('requireAdmin')) {
    function requireAdmin() { return requireEmployeeAdmin(); }
}

// Customer auth wrappers
if (!function_exists('login')) {
    function login() { $ctl = new AuthController(); return $ctl->login(); }
}
if (!function_exists('register')) {
    function register() { $ctl = new AuthController(); return $ctl->register(); }
}
if (!function_exists('logout')) {
    function logout(): void { $ctl = new AuthController(); $ctl->logout(); }
}
if (!function_exists('requireLogin')) {
    function requireLogin(): void { $ctl = new AuthController(); $ctl->requireLogin(); }
}
if (!function_exists('redirect')) {
    function redirect(string $to) { $ctl = new AuthController(); return $ctl->redirect($to); }
}

// Customer action wrapper
if (!function_exists('routeCustomerActions')) {
    function routeCustomerActions($page)
    {
        $ctl = new CustomerController();
        return $ctl->routeCustomerActions($page);
    }
}

// Page controller wrappers (prepare* helpers)
if (!function_exists('prepareProductsPage')) {
    function prepareProductsPage(): array
    {
        return PageController::prepareProductsPage();
    }
}
if (!function_exists('prepareProductViewPage')) {
    function prepareProductViewPage(int $id): array
    {
        return PageController::prepareProductViewPage($id);
    }
}
if (!function_exists('prepareHeader')) {
    function prepareHeader(): array
    {
        return PageController::prepareHeader();
    }
}
if (!function_exists('prepareHome')) {
    function prepareHome(): array
    {
        return PageController::prepareHome();
    }
}
if (!function_exists('prepareCart')) {
    function prepareCart(): array
    {
        return PageController::prepareCart();
    }
}
if (!function_exists('prepareCheckout')) {
    function prepareCheckout(): array
    {
        return PageController::prepareCheckout();
    }
}
if (!function_exists('prepareAccount')) {
    function prepareAccount(): array
    {
        return PageController::prepareAccount();
    }
}

// Employee page wrappers
if (!function_exists('prepareEmployeeDashboard')) {
    function prepareEmployeeDashboard(string $period = 'month'): array
    {
        return EmployeePageController::prepareEmployeeDashboard($period);
    }
}
if (!function_exists('prepareEmployeeProducts')) {
    function prepareEmployeeProducts(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeeProducts($subpage, $id);
    }
}
if (!function_exists('prepareEmployeeOrders')) {
    function prepareEmployeeOrders(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeeOrders($subpage, $id);
    }
}
if (!function_exists('prepareEmployeeCustomers')) {
    function prepareEmployeeCustomers(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeeCustomers($subpage, $id);
    }
}
if (!function_exists('prepareEmployeeEmployees')) {
    function prepareEmployeeEmployees(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeeEmployees($subpage, $id);
    }
}
if (!function_exists('prepareEmployeeProfile')) {
    function prepareEmployeeProfile(int $employee_id): array
    {
        return EmployeePageController::prepareEmployeeProfile($employee_id);
    }
}
if (!function_exists('prepareEmployeeReports')) {
    function prepareEmployeeReports(?string $startDate = null, ?string $endDate = null): array
    {
        return EmployeePageController::prepareEmployeeReports($startDate, $endDate);
    }
}
if (!function_exists('prepareEmployeeSettings')) {
    function prepareEmployeeSettings(): array
    {
        return EmployeePageController::prepareEmployeeSettings();
    }
}

if (!function_exists('prepareEmployeePublishers')) {
    function prepareEmployeePublishers(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeePublishers($subpage, $id);
    }
}

if (!function_exists('prepareEmployeeProviders')) {
    function prepareEmployeeProviders(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeeProviders($subpage, $id);
    }
}

if (!function_exists('prepareEmployeeCategories')) {
    function prepareEmployeeCategories(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeeCategories($subpage, $id);
    }
}

if (!function_exists('prepareEmployeePromotions')) {
    function prepareEmployeePromotions(string $subpage = 'list', ?int $id = null): array
    {
        return EmployeePageController::prepareEmployeePromotions($subpage, $id);
    }
}

