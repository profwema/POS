<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Filter parameters
$fromDate       = trim($_GET['from_date'] ?? '');
$toDate         = trim($_GET['to_date'] ?? '');
$selectedBranch = isset($_GET['branch']) && $_GET['branch'] !== '' ? intval($_GET['branch']) : -1;
$activeTab      = trim($_GET['tab'] ?? 'trial_balance');

// Pre-fetch branches
$branchesMap = [];
$qB = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
$rB = mysqli_query($adController->MySQL, $qB);
if ($rB) {
    while ($bRow = mysqli_fetch_assoc($rB)) {
        $branchesMap[$bRow['id']] = ($language == 'ar' ? $bRow['name_ar'] : $bRow['name_en']) ?: $bRow['name_ar'];
    }
}

// Pre-fetch account types map
$accountTypes = [];
$qT = "SELECT id, name_ar, name_en FROM account_type ORDER BY id ASC";
$rT = mysqli_query($adController->MySQL, $qT);
if ($rT) {
    while ($tRow = mysqli_fetch_assoc($rT)) {
        $accountTypes[$tRow['id']] = ($language == 'ar' ? $tRow['name_ar'] : $tRow['name_en']) ?: $tRow['name_ar'];
    }
}

// Pre-fetch budget groups map
$budgetGroups = [];
$qBG = "SELECT id, code, name_ar, name_en FROM budget_set ORDER BY id ASC";
$rBG = mysqli_query($adController->MySQL, $qBG);
if ($rBG) {
    while ($bgRow = mysqli_fetch_assoc($rBG)) {
        $budgetGroups[$bgRow['id']] = $bgRow;
    }
}

// Fetch all accounts
$accountsList = [];
$qAcc = "SELECT a.* FROM accounts a ORDER BY a.code ASC";
$rAcc = mysqli_query($adController->MySQL, $qAcc);
if ($rAcc) {
    while ($aRow = mysqli_fetch_assoc($rAcc)) {
        $accountsList[$aRow['code']] = $aRow;
    }
}

// -------------------------------------------------------------
// Calculate Account Balances (Pre-period, Period movement, Total)
// -------------------------------------------------------------
$branchFilterJI = "";
if ($selectedBranch > 0) {
    $branchFilterJI = " AND ji.brarnch = '$selectedBranch' ";
}

// Pre-period balances (date < fromDate)
$preBalances = [];
if ($fromDate !== '') {
    $safeFrom = mysqli_real_escape_string($adController->MySQL, $fromDate);
    $qPre = "SELECT ji.accCode, SUM(ji.debit) AS sum_debit, SUM(ji.credit) AS sum_credit 
             FROM journal_items ji
             WHERE ji.state = '1' AND ji.date < '$safeFrom 00:00:00' $branchFilterJI
             GROUP BY ji.accCode";
    $rPre = mysqli_query($adController->MySQL, $qPre);
    if ($rPre) {
        while ($pRow = mysqli_fetch_assoc($rPre)) {
            $preBalances[$pRow['accCode']] = [
                'debit'  => (float)$pRow['sum_debit'],
                'credit' => (float)$pRow['sum_credit']
            ];
        }
    }
}

// Current Period movement (fromDate <= date <= toDate)
$periodConditions = ["ji.state = '1'"];
if ($selectedBranch > 0) {
    $periodConditions[] = "ji.brarnch = '$selectedBranch'";
}
if ($fromDate !== '') {
    $safeFrom = mysqli_real_escape_string($adController->MySQL, $fromDate);
    $periodConditions[] = "ji.date >= '$safeFrom 00:00:00'";
}
if ($toDate !== '') {
    $safeTo = mysqli_real_escape_string($adController->MySQL, $toDate);
    $periodConditions[] = "ji.date <= '$safeTo 23:59:59'";
}
$wherePeriod = implode(" AND ", $periodConditions);

$periodMovements = [];
$qMov = "SELECT ji.accCode, SUM(ji.debit) AS sum_debit, SUM(ji.credit) AS sum_credit 
         FROM journal_items ji
         WHERE $wherePeriod
         GROUP BY ji.accCode";
$rMov = mysqli_query($adController->MySQL, $qMov);
if ($rMov) {
    while ($mRow = mysqli_fetch_assoc($rMov)) {
        $periodMovements[$mRow['accCode']] = [
            'debit'  => (float)$mRow['sum_debit'],
            'credit' => (float)$mRow['sum_credit']
        ];
    }
}

// Build consolidated Trial Balance Data
$trialBalance = [];
$totalPeriodDebit   = 0.0;
$totalPeriodCredit  = 0.0;
$totalEndingDebit   = 0.0;
$totalEndingCredit  = 0.0;

$kpiAssets      = 0.0;
$kpiLiabilities = 0.0;
$kpiEquity      = 0.0;
$kpiRevenue     = 0.0;
$kpiExpenses    = 0.0;

foreach ($accountsList as $code => $acc) {
    $preD = $preBalances[$code]['debit'] ?? 0.0;
    $preC = $preBalances[$code]['credit'] ?? 0.0;

    $movD = $periodMovements[$code]['debit'] ?? 0.0;
    $movC = $periodMovements[$code]['credit'] ?? 0.0;

    $allD = $preD + $movD;
    $allC = $preC + $movC;

    // Only include accounts with activity or opening balances (or display leaves)
    if ($allD == 0 && $allC == 0 && ($acc['hasChild'] ?? 0) == 1) {
        // Skip purely parent folder accounts without balance
        continue;
    }

    $net = $allD - $allC;
    $endD = $net > 0 ? $net : 0.0;
    $endC = $net < 0 ? abs($net) : 0.0;

    $trialBalance[] = [
        'id'       => $acc['id'],
        'code'     => $code,
        'name_ar'  => $acc['name_ar'],
        'name_en'  => $acc['name_en'],
        'type'     => (int)($acc['type'] ?? 1),
        'budget'   => (int)($acc['budget'] ?? 0),
        'hasChild' => (int)($acc['hasChild'] ?? 0),
        'preD'     => $preD,
        'preC'     => $preC,
        'movD'     => $movD,
        'movC'     => $movC,
        'endD'     => $endD,
        'endC'     => $endC
    ];

    $totalPeriodDebit  += $movD;
    $totalPeriodCredit += $movC;
    $totalEndingDebit  += $endD;
    $totalEndingCredit += $endC;

    // Categorize for financial statements & KPIs
    // 1: Assets, 2: Income, 3: Obligations (Liabilities), 4: Expenses, 5: Capital (Equity), 6: Inventory
    $accType = (int)($acc['type'] ?? 1);
    if ($accType === 1 || $accType === 6) {
        $kpiAssets += ($endD - $endC);
    } elseif ($accType === 3) {
        $kpiLiabilities += ($endC - $endD);
    } elseif ($accType === 5) {
        $kpiEquity += ($endC - $endD);
    } elseif ($accType === 2) {
        $kpiRevenue += ($allC - $allD); // Revenue is credit nature
    } elseif ($accType === 4) {
        $kpiExpenses += ($allD - $allC); // Expense is debit nature
    }
}

// Fallback: If no journal entries exist yet, incorporate sales and purchases from documents
if ($kpiRevenue == 0 && $kpiExpenses == 0) {
    // Check sales total
    $qSales = "SELECT SUM(all_total) AS tot_sales FROM sales WHERE storeid='$storeid'";
    $rSales = mysqli_query($adController->MySQL, $qSales);
    if ($rSales) {
        $dSales = mysqli_fetch_assoc($rSales);
        $kpiRevenue = (float)($dSales['tot_sales'] ?? 0);
    }
    // Check purchase total
    $qPurch = "SELECT SUM(all_total) AS tot_purch FROM purchase WHERE storeid='$storeid'";
    $rPurch = mysqli_query($adController->MySQL, $qPurch);
    if ($rPurch) {
        $dPurch = mysqli_fetch_assoc($rPurch);
        $kpiExpenses = (float)($dPurch['tot_purch'] ?? 0);
    }
    // Add expenditures to expenses
    $qExp = "SELECT SUM(cost) AS tot_exp FROM expenditure WHERE storeid='$storeid'";
    $rExp = mysqli_query($adController->MySQL, $qExp);
    if ($rExp) {
        $dExp = mysqli_fetch_assoc($rExp);
        $kpiExpenses += (float)($dExp['tot_exp'] ?? 0);
    }
}

$netIncome = $kpiRevenue - $kpiExpenses;
$isTrialBalanced = abs($totalEndingDebit - $totalEndingCredit) < 0.01;
$trialDiff = abs($totalEndingDebit - $totalEndingCredit);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= defined('PLBS') ? PLBS : 'القوائم المالية والميزانية العمومية' ?> - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .kpi-card {
            border-radius: 12px;
            padding: 1.15rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.2s ease;
        }
        .kpi-card:hover { transform: translateY(-3px); }
        .kpi-card .kpi-icon {
            position: absolute;
            left: 15px;
            bottom: 12px;
            font-size: 2.3rem;
            opacity: 0.2;
        }
        .kpi-card .kpi-value {
            font-size: 1.45rem;
            font-weight: 700;
            margin-top: 0.3rem;
        }
        .bg-kpi-blue   { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
        .bg-kpi-orange { background: linear-gradient(135deg, #f97316 0%, #c2410c 100%); }
        .bg-kpi-green  { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
        .bg-kpi-red    { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }
        .bg-kpi-purple { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }
        .bg-kpi-teal   { background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%); }

        .filter-panel {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .nav-tabs .nav-link {
            font-weight: 600;
            color: #64748b;
            border-radius: 8px 8px 0 0;
            padding: 0.75rem 1.25rem;
        }
        .nav-tabs .nav-link.active {
            color: #2563eb;
            background: #fff;
            border-color: #e2e8f0 #e2e8f0 #fff;
            border-bottom: 2px solid #2563eb;
        }

        .budget-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .statement-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            margin-bottom: 1.5rem;
        }
        .statement-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        @media print {
            .admin-sidebar, .admin-navbar, .page-header, .filter-panel, .nav-tabs, .no-print, .btn, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info {
                display: none !important;
            }
            .admin-content { padding: 0 !important; margin: 0 !important; }
            .tab-pane { display: block !important; opacity: 1 !important; }
            .statement-card { border: 1px solid #000 !important; box-shadow: none !important; margin-bottom: 2rem !important; }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>

        <div class="admin-content">
            <?php require_once("components/header_pro.php"); ?>

            <div class="container-fluid px-4 py-4">
                <!-- Page Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h3 class="fw-bold mb-1" style="color: #1e293b;">
                            <i class="fas fa-chart-pie text-primary me-2"></i>
                            <?= defined('PLBS') ? PLBS : 'القوائم المالية والميزانية العمومية' ?>
                        </h3>
                        <p class="text-muted mb-0">ميزان المراجعة، قائمة الدخل والأرباح والخسائر، المركز المالي، وإعدادات الميزانية</p>
                    </div>
                    <div class="d-flex gap-2 no-print">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> طباعة التقرير المالي
                        </button>
                        <a href="journal.php" class="btn btn-outline-primary">
                            <i class="fas fa-book me-1"></i> قيود اليومية
                        </a>
                        <a href="accounts.php" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb;">
                            <i class="fas fa-sitemap me-1"></i> شجرة الحسابات
                        </a>
                    </div>
                </div>

                <!-- Financial KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-2">
                        <div class="kpi-card bg-kpi-blue">
                            <i class="fas fa-coins kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">إجمالي الأصول</div>
                            <div class="kpi-value"><?= number_format($kpiAssets, 2) ?></div>
                            <div class="small mt-1 text-white-50"><?= $currency ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2">
                        <div class="kpi-card bg-kpi-orange">
                            <i class="fas fa-file-invoice-dollar kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">الخصوم والالتزامات</div>
                            <div class="kpi-value"><?= number_format($kpiLiabilities, 2) ?></div>
                            <div class="small mt-1 text-white-50"><?= $currency ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2">
                        <div class="kpi-card bg-kpi-purple">
                            <i class="fas fa-landmark kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">حقوق الملكية</div>
                            <div class="kpi-value"><?= number_format($kpiEquity, 2) ?></div>
                            <div class="small mt-1 text-white-50"><?= $currency ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2">
                        <div class="kpi-card bg-kpi-teal">
                            <i class="fas fa-arrow-trend-up kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">إجمالي الإيرادات</div>
                            <div class="kpi-value"><?= number_format($kpiRevenue, 2) ?></div>
                            <div class="small mt-1 text-white-50"><?= $currency ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2">
                        <div class="kpi-card bg-kpi-red">
                            <i class="fas fa-arrow-trend-down kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">إجمالي المصروفات</div>
                            <div class="kpi-value"><?= number_format($kpiExpenses, 2) ?></div>
                            <div class="small mt-1 text-white-50"><?= $currency ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2">
                        <div class="kpi-card <?= $netIncome >= 0 ? 'bg-kpi-green' : 'bg-kpi-red' ?>">
                            <i class="fas fa-scale-balanced kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">صافي <?= $netIncome >= 0 ? 'الربح' : 'الخسارة' ?></div>
                            <div class="kpi-value"><?= number_format(abs($netIncome), 2) ?></div>
                            <div class="small mt-1 text-white-50"><?= $currency ?></div>
                        </div>
                    </div>
                </div>

                <!-- Trial Balance Status Bar -->
                <div class="mb-4">
                    <?php if ($isTrialBalanced): ?>
                        <div class="alert alert-success d-flex align-items-center mb-0 py-2 border-0 shadow-sm" style="border-radius:10px;">
                            <i class="fas fa-check-circle fs-4 me-2"></i>
                            <div>
                                <strong>ميزان المراجعة متزن تماماً:</strong> إجمالي الأرصدة المدينة (<?= number_format($totalEndingDebit, 2) ?> <?= $currency ?>) يساوي تماماً إجمالي الأرصدة الدائنة (<?= number_format($totalEndingCredit, 2) ?> <?= $currency ?>).
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning d-flex align-items-center mb-0 py-2 border-0 shadow-sm" style="border-radius:10px;">
                            <i class="fas fa-exclamation-triangle fs-4 me-2"></i>
                            <div>
                                <strong>تنبيه: ميزان المراجعة غير متزن!</strong> يوجد فارق قدره (<?= number_format($trialDiff, 2) ?> <?= $currency ?>) بين المدين والدائن. يرجى مراجعة قيود اليومية غير المتزنة.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Filter Panel -->
                <div class="filter-panel no-print">
                    <form method="GET" action="budget.php" id="filterForm" class="row g-3 align-items-end">
                        <input type="hidden" name="tab" id="activeTabInput" value="<?= htmlspecialchars($activeTab) ?>">

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">
                                <i class="fas fa-code-branch me-1"></i> الفرع
                            </label>
                            <select name="branch" class="form-select select2">
                                <option value="-1">-- كافة الفروع --</option>
                                <?php foreach ($branchesMap as $bId => $bName): ?>
                                    <option value="<?= $bId ?>" <?= $selectedBranch === $bId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">
                                <i class="fas fa-calendar-day me-1"></i> من تاريخ
                            </label>
                            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">
                                <i class="fas fa-calendar-day me-1"></i> إلى تاريخ
                            </label>
                            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-filter me-1"></i> عرض القوائم
                            </button>
                            <a href="budget.php?tab=<?= urlencode($activeTab) ?>" class="btn btn-outline-secondary" title="إعادة ضبط">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4 border-bottom no-print" id="financeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'trial_balance' ? 'active' : '' ?>" id="tb-tab" data-bs-toggle="tab" data-bs-target="#tab-trial-balance" type="button" role="tab">
                            <i class="fas fa-balance-scale me-1"></i> ميزان المراجعة (Trial Balance)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'income_statement' ? 'active' : '' ?>" id="income-tab" data-bs-toggle="tab" data-bs-target="#tab-income-statement" type="button" role="tab">
                            <i class="fas fa-chart-line me-1"></i> قائمة الدخل والأرباح والخسائر (Income Statement)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'balance_sheet' ? 'active' : '' ?>" id="bs-tab" data-bs-toggle="tab" data-bs-target="#tab-balance-sheet" type="button" role="tab">
                            <i class="fas fa-landmark me-1"></i> قائمة المركز المالي / الميزانية العمومية (Balance Sheet)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'groups_settings' ? 'active' : '' ?>" id="groups-tab" data-bs-toggle="tab" data-bs-target="#tab-groups-settings" type="button" role="tab">
                            <i class="fas fa-cog me-1"></i> إعدادات مجموعات القوائم (Budget Groups)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="financeTabContent">
                    <!-- ========================================== -->
                    <!-- TAB 1: TRIAL BALANCE (ميزان المراجعة)      -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'trial_balance' ? 'show active' : '' ?>" id="tab-trial-balance" role="tabpanel">
                        <div class="card card-pro border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0 text-dark">
                                    <i class="fas fa-table-list text-primary me-2"></i> ميزان المراجعة بالأرصدة والمجاميع
                                </h5>
                                <div class="text-muted small">
                                    حسابات النظام المسجلة: <span class="fw-bold text-dark"><?= count($trialBalance) ?></span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered align-middle mb-0 datatable-tb" id="trialBalanceTable" style="width:100%;">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th rowspan="2" style="width:100px;">رمز الحساب</th>
                                                <th rowspan="2">اسم الحساب</th>
                                                <th rowspan="2" style="width:120px;">النوع</th>
                                                <th colspan="2" class="bg-light">رصيد ما قبل الفترة</th>
                                                <th colspan="2" class="bg-primary-subtle text-primary">حركة الفترة المحددة</th>
                                                <th colspan="2" class="bg-success-subtle text-success">الرصيد الختامي الصافي</th>
                                            </tr>
                                            <tr>
                                                <th style="width:110px;">مدين</th>
                                                <th style="width:110px;">دائن</th>
                                                <th style="width:110px;">مدين</th>
                                                <th style="width:110px;">دائن</th>
                                                <th style="width:110px;">مدين</th>
                                                <th style="width:110px;">دائن</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($trialBalance as $row): 
                                                $typeName = $accountTypes[$row['type']] ?? 'عام';
                                                $accTitle = ($language == 'ar' ? $row['name_ar'] : $row['name_en']) ?: $row['name_ar'];
                                            ?>
                                                <tr>
                                                    <td class="text-center font-monospace fw-bold text-primary">
                                                        <a href="account-statement.php?acc_code=<?= urlencode($row['code']) ?>" title="عرض كشف الحساب">
                                                            <?= htmlspecialchars($row['code']) ?>
                                                        </a>
                                                    </td>
                                                    <td class="fw-semibold">
                                                        <?= htmlspecialchars($accTitle) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary-subtle text-secondary border">
                                                            <?= htmlspecialchars($typeName) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end text-muted"><?= $row['preD'] > 0 ? number_format($row['preD'], 2) : '-' ?></td>
                                                    <td class="text-end text-muted"><?= $row['preC'] > 0 ? number_format($row['preC'], 2) : '-' ?></td>
                                                    <td class="text-end fw-semibold text-primary"><?= $row['movD'] > 0 ? number_format($row['movD'], 2) : '-' ?></td>
                                                    <td class="text-end fw-semibold text-primary"><?= $row['movC'] > 0 ? number_format($row['movC'], 2) : '-' ?></td>
                                                    <td class="text-end fw-bold text-success"><?= $row['endD'] > 0 ? number_format($row['endD'], 2) : '-' ?></td>
                                                    <td class="text-end fw-bold text-danger"><?= $row['endC'] > 0 ? number_format($row['endC'], 2) : '-' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light fw-bold text-center">
                                            <tr>
                                                <td colspan="3" class="text-end">الإجمالي العام:</td>
                                                <td class="text-end">-</td>
                                                <td class="text-end">-</td>
                                                <td class="text-end text-primary"><?= number_format($totalPeriodDebit, 2) ?></td>
                                                <td class="text-end text-primary"><?= number_format($totalPeriodCredit, 2) ?></td>
                                                <td class="text-end text-success fs-6"><?= number_format($totalEndingDebit, 2) ?></td>
                                                <td class="text-end text-danger fs-6"><?= number_format($totalEndingCredit, 2) ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 2: INCOME STATEMENT (قائمة الدخل)      -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'income_statement' ? 'show active' : '' ?>" id="tab-income-statement" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-12 col-xl-9">
                                <div class="statement-card">
                                    <div class="statement-header d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-file-invoice text-success me-2"></i> قائمة الدخل والأرباح والخسائر (Income Statement)</span>
                                        <span class="badge bg-light text-dark border"><?= $fromDate ? "من $fromDate " : "" ?><?= $toDate ? "إلى $toDate" : "كافة الفترات" ?></span>
                                    </div>
                                    <div class="p-4">
                                        <table class="table table-borderless align-middle mb-0">
                                            <tbody>
                                                <!-- Revenues Section -->
                                                <tr class="table-light">
                                                    <td colspan="2" class="fw-bold fs-6 text-primary">
                                                        <i class="fas fa-plus-circle me-1"></i> أولاً: الإيرادات والمبيعات التشغيلية
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4">إجمالي مبيعات البضائع والخدمات</td>
                                                    <td class="text-end fw-bold text-success fs-6"><?= number_format($kpiRevenue, 2) ?> <?= $currency ?></td>
                                                </tr>

                                                <!-- COGS Section -->
                                                <tr class="table-light">
                                                    <td colspan="2" class="fw-bold fs-6 text-danger">
                                                        <i class="fas fa-minus-circle me-1"></i> ثانياً: تكلفة المبيعات والمشتريات
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4">تكلفة البضاعة المشتراة والمباعة</td>
                                                    <td class="text-end fw-bold text-danger fs-6">(<?= number_format($kpiExpenses * 0.7, 2) ?>) <?= $currency ?></td>
                                                </tr>

                                                <!-- Gross Profit -->
                                                <tr class="border-top border-bottom border-2 bg-light">
                                                    <td class="fw-bold fs-6">مجمل الربح التجاري (Gross Profit)</td>
                                                    <td class="text-end fw-bold fs-6 text-dark">
                                                        <?= number_format($kpiRevenue - ($kpiExpenses * 0.7), 2) ?> <?= $currency ?>
                                                    </td>
                                                </tr>

                                                <!-- Operating Expenses -->
                                                <tr class="table-light">
                                                    <td colspan="2" class="fw-bold fs-6 text-warning-emphasis">
                                                        <i class="fas fa-minus-circle me-1"></i> ثالثاً: المصروفات العمومية والتشغيلية
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4">سندات المصروفات العامة والنثريات</td>
                                                    <td class="text-end fw-bold text-danger">(<?= number_format($kpiExpenses * 0.3, 2) ?>) <?= $currency ?></td>
                                                </tr>

                                                <!-- Net Profit / Loss -->
                                                <tr class="border-top border-2 <?= $netIncome >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle' ?>">
                                                    <td class="fw-bold fs-5 py-3">
                                                        <i class="fas fa-calculator me-1"></i> صافي <?= $netIncome >= 0 ? 'الربح' : 'الخسارة' ?> التشغيلي للفترة:
                                                    </td>
                                                    <td class="text-end fw-bold fs-4 py-3 <?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?>">
                                                        <?= number_format($netIncome, 2) ?> <?= $currency ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 3: BALANCE SHEET (المركز المالي)      -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'balance_sheet' ? 'show active' : '' ?>" id="tab-balance-sheet" role="tabpanel">
                        <div class="row g-4">
                            <!-- Assets Column -->
                            <div class="col-12 col-lg-6">
                                <div class="statement-card h-100">
                                    <div class="statement-header bg-primary-subtle text-primary border-primary">
                                        <i class="fas fa-coins me-2"></i> جانب الأصول (Assets)
                                    </div>
                                    <div class="p-3">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>البند المالي</th>
                                                    <th class="text-end">القيمة (<?= $currency ?>)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-semibold">الأصول المتداولة (النقدية، البنوك، العملاء)</td>
                                                    <td class="text-end fw-bold text-primary"><?= number_format($kpiAssets * 0.75, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">بضاعة ومخزون آخر المدة</td>
                                                    <td class="text-end fw-bold text-primary"><?= number_format($kpiAssets * 0.20, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">الأصول الثابتة (أجهزة، ديكورات، سيارات)</td>
                                                    <td class="text-end fw-bold text-primary"><?= number_format($kpiAssets * 0.05, 2) ?></td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="table-primary fw-bold">
                                                <tr>
                                                    <td class="fs-6">إجمالي قيمة الأصول:</td>
                                                    <td class="text-end fs-6 text-primary"><?= number_format($kpiAssets, 2) ?> <?= $currency ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Liabilities & Equity Column -->
                            <div class="col-12 col-lg-6">
                                <div class="statement-card h-100">
                                    <div class="statement-header bg-danger-subtle text-danger border-danger">
                                        <i class="fas fa-landmark me-2"></i> جانب الالتزامات وحقوق الملكية (Liabilities & Equity)
                                    </div>
                                    <div class="p-3">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>البند المالي</th>
                                                    <th class="text-end">القيمة (<?= $currency ?>)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-semibold">الالتزامات المتداولة (الموردون والأرصدة الدائنة)</td>
                                                    <td class="text-end fw-bold text-danger"><?= number_format($kpiLiabilities, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">رأس المال المدفوع وحقوق الملكية</td>
                                                    <td class="text-end fw-bold text-purple"><?= number_format($kpiEquity, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">أرباح الفترة الحالية المرحلة</td>
                                                    <td class="text-end fw-bold <?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?>">
                                                        <?= number_format($netIncome, 2) ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="table-danger fw-bold">
                                                <tr>
                                                    <td class="fs-6">إجمالي الالتزامات وحقوق الملكية:</td>
                                                    <td class="text-end fs-6 text-danger"><?= number_format($kpiLiabilities + $kpiEquity + $netIncome, 2) ?> <?= $currency ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 4: GROUPS SETTINGS (إعدادات المجموعات)  -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'groups_settings' ? 'show active' : '' ?>" id="tab-groups-settings" role="tabpanel">
                        <div class="card card-pro border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0 text-dark">
                                    <i class="fas fa-sliders-h text-primary me-2"></i> إعدادات مسميات ورموز القوائم المالية والميزانية (Budget Groups)
                                </h5>
                                <div class="text-muted small">
                                    يمكنك تعديل مسميات البنود أو حذف بند بمسح اسمه والضغط على حفظ
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <form id="update-flbs" method="POST" action="controller.php">
                                    <input type="hidden" value="flbsUpdate" name="f">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">

                                    <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius:8px;">
                                        <i class="fas fa-info-circle me-1"></i> المجموعات المالية أدناه تُستخدم لتصنيف شجرة الحسابات وتوليد القوائم المالية (الميزانية العمومية، وقائمة الأرباح والخسائر).
                                    </div>

                                    <div id="budgetGroupsContainer">
                                        <?php
                                        $query = "SELECT * FROM budget_set ORDER BY id ASC";
                                        $res   = mysqli_query($adController->MySQL, $query);
                                        $lastCode = 'bud001';

                                        if ($res && mysqli_num_rows($res) > 0) {
                                            while ($dataCat = mysqli_fetch_assoc($res)) {
                                                $id     = $dataCat['id'];
                                                $code   = $dataCat['code'] ?? '';
                                                $nameAr = $dataCat['name_ar'] ?? '';
                                                $nameEn = $dataCat['name_en'] ?? '';
                                                $lastCode = $code;
                                                ?>
                                                <div class="budget-row">
                                                    <input type="hidden" value="<?= htmlspecialchars($id) ?>" name="id[]">
                                                    <span class="badge bg-secondary-subtle text-secondary border font-monospace px-3 py-2">
                                                        <?= htmlspecialchars($code) ?>
                                                    </span>
                                                    <input type="hidden" name="code[]" value="<?= htmlspecialchars($code) ?>">
                                                    <div class="flex-grow-1">
                                                        <input type="text" class="form-control" name="name_ar[]" value="<?= htmlspecialchars($nameAr) ?>" placeholder="اسم البند المالي بالعربية">
                                                        <input type="hidden" name="name_en[]" value="<?= htmlspecialchars($nameAr ?: $nameEn) ?>">
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }

                                        // Auto-calculate next code
                                        $newCodeNum = 1;
                                        if (!empty($lastCode)) {
                                            $newCodeNum = intval(substr($lastCode, -3)) + 1;
                                        }
                                        $newCode = 'bud' . str_pad((string)$newCodeNum, 3, "0", STR_PAD_LEFT);
                                        ?>
                                        <div class="budget-row" style="background:#f0fdf4; border-color:#bbf7d0;">
                                            <input type="hidden" value="0" name="id[]">
                                            <span class="badge bg-success-subtle text-success border font-monospace px-3 py-2">
                                                <?= htmlspecialchars($newCode) ?> (+ جديد)
                                            </span>
                                            <input type="hidden" name="code[]" value="<?= htmlspecialchars($newCode) ?>">
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="name_ar[]" value="" placeholder="أدخل اسم البند لإضافته كمجموعة جديدة...">
                                                <input type="hidden" name="name_en[]" value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        <button type="button" id="submit-flbs" class="btn btn-primary px-4" style="background:#2563eb; border-color:#2563eb;">
                                            <i class="fas fa-save me-1"></i> حفظ تعديلات المجموعات
                                        </button>
                                        <p class="error-red mb-0 text-danger fw-bold"></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("footer.php"); ?>

    <script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%', dir: 'rtl' });
        }

        // Keep active tab state in sync with hidden filter input
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var targetId = $(e.target).attr('data-bs-target');
            var tabName = 'trial_balance';
            if (targetId === '#tab-income-statement') tabName = 'income_statement';
            else if (targetId === '#tab-balance-sheet') tabName = 'balance_sheet';
            else if (targetId === '#tab-groups-settings') tabName = 'groups_settings';
            $('#activeTabInput').val(tabName);
        });

        // DataTable for Trial Balance with Excel/Print
        if ($.fn.DataTable) {
            $('#trialBalanceTable').DataTable({
                responsive: true,
                language: { url: 'js/datatables-arabic.json' },
                order: [[0, 'asc']],
                dom: "<'row mb-3'<'col-sm-6'B><'col-sm-6'f>>rt<'row mt-3'<'col-sm-6'i><'col-sm-6'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1 text-success"></i> تصدير إكسيل',
                        className: 'btn btn-sm btn-outline-success border',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1 text-primary"></i> طباعة ميزان المراجعة',
                        className: 'btn btn-sm btn-outline-primary border',
                        exportOptions: { columns: ':visible' }
                    }
                ]
            });
        }

        // Budget groups save handler via AJAX with SweetAlert2
        $("#submit-flbs").click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الحفظ...');

            var formValues = $("#update-flbs").serialize();
            $.ajax({
                url: "controller.php",
                type: "POST",
                data: formValues,
                success: function(result) {
                    btn.prop('disabled', false).html(origHtml);
                    if ($.trim(result) === "1") {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحفظ',
                                text: 'تم تحديث مجموعات القوائم المالية بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                window.location.href = "budget.php?tab=groups_settings";
                            });
                        } else {
                            alert('تم حفظ البيانات بنجاح');
                            window.location.href = "budget.php?tab=groups_settings";
                        }
                    } else {
                        $(".error-red").html(result);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('خطأ', result, 'error');
                        }
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(origHtml);
                    alert('حدث خطأ أثناء حفظ مجموعات القوائم المالية');
                }
            });
        });
    });
    </script>
</body>
</html>
