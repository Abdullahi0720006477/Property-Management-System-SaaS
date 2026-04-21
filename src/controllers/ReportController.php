<?php
/**
 * Report Controller
 */
require_once SRC_PATH . '/models/Payment.php';
require_once SRC_PATH . '/models/Expense.php';
require_once SRC_PATH . '/models/Property.php';
require_once SRC_PATH . '/models/Unit.php';
require_once SRC_PATH . '/models/Lease.php';

requireRole('company_admin', 'manager', 'accountant');

$paymentModel  = new Payment();
$expenseModel  = new Expense();
$propertyModel = new Property();
$unitModel     = new Unit();
$leaseModel    = new Lease();
$cid = companyId();

$action = $action ?? 'index';

// Default date range: start of current year to today
$startDate = getData('start_date') ?: date('Y-01-01');
$endDate   = getData('end_date') ?: date('Y-m-d');

switch ($action) {
    case 'income':
        $incomeByProperty = $paymentModel->getIncomeByProperty($startDate, $endDate, $cid);
        $monthlyRevenue   = $paymentModel->getMonthlyRevenue(12, $cid);
        $totalIncome      = array_sum(array_column($incomeByProperty, 'total_income'));

        $pageTitle = 'Income Report';
        require_once VIEWS_PATH . '/reports/income.php';
        break;

    case 'expenses':
        $expensesByProperty = $expenseModel->getTotalByProperty($startDate, $endDate, $cid);
        $expensesByCategory = $expenseModel->getTotalByCategory($startDate, $endDate, $cid);
        $totalExpenses      = array_sum(array_column($expensesByProperty, 'total_expense'));

        $pageTitle = 'Expense Report';
        require_once VIEWS_PATH . '/reports/expenses.php';
        break;

    case 'occupancy':
        $properties = $propertyModel->getAll('', '', '', null, 1000, 0, $cid);
        $totalUnitsAll    = 0;
        $totalOccupiedAll = 0;
        foreach ($properties as $p) {
            $totalUnitsAll    += (int) $p['unit_count'];
            $totalOccupiedAll += (int) $p['occupied_count'];
        }
        $overallOccupancy = $totalUnitsAll > 0 ? round(($totalOccupiedAll / $totalUnitsAll) * 100, 1) : 0;

        $pageTitle = 'Occupancy Report';
        require_once VIEWS_PATH . '/reports/occupancy.php';
        break;

    case 'overdue':
        $overduePayments = $paymentModel->getOverduePayments($cid);
        $totalOverdue    = array_sum(array_column($overduePayments, 'amount'));

        $pageTitle = 'Overdue Payments Report';
        require_once VIEWS_PATH . '/reports/overdue.php';
        break;

    case 'export_csv':
        $type = getData('type');

        switch ($type) {
            case 'income':
                $data = $paymentModel->getIncomeByProperty($startDate, $endDate, $cid);
                $filename = 'income_report_' . $startDate . '_to_' . $endDate . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Property', 'Total Income']);
                $total = 0;
                foreach ($data as $row) {
                    fputcsv($output, [$row['property_name'], $row['total_income']]);
                    $total += $row['total_income'];
                }
                fputcsv($output, ['TOTAL', $total]);
                fclose($output);
                exit;

            case 'expenses':
                $data = $expenseModel->getTotalByProperty($startDate, $endDate, $cid);
                $filename = 'expense_report_' . $startDate . '_to_' . $endDate . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Property', 'Total Expenses']);
                $total = 0;
                foreach ($data as $row) {
                    fputcsv($output, [$row['property_name'], $row['total_expense']]);
                    $total += $row['total_expense'];
                }
                fputcsv($output, ['TOTAL', $total]);
                fclose($output);
                exit;

            case 'occupancy':
                $data = $propertyModel->getAll('', '', '', null, 1000, 0, $cid);
                $filename = 'occupancy_report_' . date('Y-m-d') . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Property', 'Total Units', 'Occupied', 'Vacant', 'Occupancy %']);
                foreach ($data as $row) {
                    $unitCount = (int) $row['unit_count'];
                    $occupied  = (int) $row['occupied_count'];
                    $vacant    = $unitCount - $occupied;
                    $pct       = $unitCount > 0 ? round(($occupied / $unitCount) * 100, 1) : 0;
                    fputcsv($output, [$row['name'], $unitCount, $occupied, $vacant, $pct . '%']);
                }
                fclose($output);
                exit;

            case 'overdue':
                $data = $paymentModel->getOverduePayments($cid);
                $filename = 'overdue_payments_' . date('Y-m-d') . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Tenant', 'Property', 'Unit', 'Amount', 'Due Date', 'Days Overdue']);
                $total = 0;
                foreach ($data as $row) {
                    $daysOverdue = ceil((time() - strtotime($row['due_date'])) / 86400);
                    fputcsv($output, [
                        $row['tenant_name'], $row['property_name'], $row['unit_number'],
                        $row['amount'], $row['due_date'], $daysOverdue
                    ]);
                    $total += $row['amount'];
                }
                fputcsv($output, ['', '', '', $total, '', '']);
                fclose($output);
                exit;

            default:
                setFlashMessage('error', 'Invalid export type.');
                redirect('?page=reports');
        }
        break;

    case 'index':
    default:
        $pageTitle = 'Reports';
        require_once VIEWS_PATH . '/reports/index.php';
        break;
}
