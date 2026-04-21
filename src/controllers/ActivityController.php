<?php
require_once SRC_PATH . '/models/ActivityLog.php';
require_once SRC_PATH . '/models/User.php';

requireRole('company_admin');

$cid = companyId();

$actionFilter = getData('action_filter') ?: null;
$userFilter = getData('user_filter') ? (int)getData('user_filter') : null;
$dateFrom = getData('date_from') ?: null;
$dateTo = getData('date_to') ?: null;

$logs = ActivityLog::getRecent(100, $actionFilter, $userFilter, $dateFrom, $dateTo, $cid);
$actionTypes = ActivityLog::getActionTypes($cid);

$userModel = new User();
$allUsers = $userModel->getAll();

$pageTitle = 'Activity Log';
require_once VIEWS_PATH . '/activity/index.php';
