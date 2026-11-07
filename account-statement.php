<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$pgName   = basename(__FILE__);

$acc = [];

function getAccounts($account)
{
    global $acc, $adController;
    $safeAccount = mysqli_real_escape_string($adController->MySQL, $account);
    $query       = "SELECT id, hasChild FROM accounts WHERE code = '$safeAccount' LIMIT 1";
    $result      = mysqli_query($adController->MySQL, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $fetchRes = mysqli_fetch_assoc($result);
        if (($fetchRes['hasChild'] ?? 0) == 0) {
            $acc[] = $account;
        } else {
            $sql   = "SELECT code FROM accounts WHERE parent = '" . intval($fetchRes['id']) . "'";
            $query = mysqli_query($adController->MySQL, $sql);
            if ($query && mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    getAccounts($row['code']);                      
                }
            }           
        }
    } else {
        $acc[] = $account;
    }
}

$selectedAcc    = trim((string)($_REQUEST['account'] ?? ($_REQUEST['acc'] ?? '')));
$selectedBranch = trim((string)($_REQUEST['branch'] ?? ''));
$fromDate       = trim((string)($_REQUEST['fr_date'] ?? ''));
$toDate         = trim((string)($_REQUEST['to_date'] ?? ''));
$isSubmitted    = isset($_REQUEST['Submit']) || ($selectedAcc !== '');

// Pre-cache accounts for dropdown
$accQuery = "SELECT a.id, a.code, a.name_ar, a.name_en, p.name_ar AS parent_ar, p.name_en AS parent_en
             FROM accounts a
             LEFT JOIN accounts p ON p.id = a.parent
             ORDER BY a.parent ASC, a.code ASC";
$resAcc = mysqli_query($adController->MySQL, $accQuery);
$accountsList = [];
if ($resAcc) {
    while ($dA = mysqli_fetch_assoc($resAcc)) {
        $name   = ($language == 'en' && !empty($dA['name_en'])) ? $dA['name_en'] : (!empty($dA['name_ar']) ? $dA['name_ar'] : 'حساب');
        $parent = ($language == 'en' && !empty($dA['parent_en'])) ? $dA['parent_en'] : (!empty($dA['parent_ar']) ? $dA['parent_ar'] : '');
        $code   = $dA['code'];
        $label  = ($parent ? $parent . " » " : "") . $name . " (" . $code . ")";
        $accountsList[] = [
            'code'  => $code,
            'label' => $label
        ];
    }
}

// Pre-cache branches
$branchesList = [];
$resB = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' OR storeid='0' ORDER BY id ASC");
if ($resB) {
    while ($dB = mysqli_fetch_assoc($resB)) {
        $branchesList[] = $dB;
    }
}

// Prepare results
$ledgerRows    = [];
$totalDebit    = 0;
$totalCredit   = 0;
$balanceDebit  = 0;
$balanceCredit = 0;

if ($isSubmitted) {
    $conditionRep = '';
    if (!empty($selectedAcc) && $selectedAcc !== '0') {
        getAccounts($selectedAcc);      
        if (!empty($acc)) {
            $escapedAccs = array_map(function($a) use ($adController) {
                return "'" . mysqli_real_escape_string($adController->MySQL, $a) . "'";
            }, $acc);
            $conditionRep .= " AND ji.accCode IN (" . implode(",", $escapedAccs) . ") ";
        }
    }

    if (!empty($selectedBranch)) {
        $cleanBranch = intval($selectedBranch);
        $conditionRep .= " AND ji.brarnch = '$cleanBranch' ";
    }

    if (!empty($fromDate)) {
        $cleanFdate = mysqli_real_escape_string($adController->MySQL, $fromDate);
        $conditionRep .= " AND ji.date >= '$cleanFdate 00:00:00' ";                 
    }

    if (!empty($toDate)) {
        $cleanTdate = mysqli_real_escape_string($adController->MySQL, $toDate);
        $conditionRep .= " AND ji.date <= '$cleanTdate 23:59:59' ";                     
    }

    $query = "SELECT ji.*, 
                     a.name_ar AS acc_name_ar, a.name_en AS acc_name_en,
                     b.name_ar AS branch_name_ar, b.name_en AS branch_name_en
              FROM journal_items ji
              LEFT JOIN accounts a ON a.code = ji.accCode
              LEFT JOIN branches b ON b.id = ji.brarnch
              WHERE ji.state = '1' $conditionRep 
              ORDER BY ji.date ASC, ji.id ASC";
    $res = mysqli_query($adController->MySQL, $query);

    $oldBalanceDebit  = 0;
    $oldBalanceCredit = 0;
    $seq = 0;

    if ($res) {
        while ($data = mysqli_fetch_assoc($res)) {
            $seq++;
            $accName = ($language == 'en' && !empty($data['acc_name_en'])) ? $data['acc_name_en'] : (!empty($data['acc_name_ar']) ? $data['acc_name_ar'] : $data['accCode']);
            $branchName = ($language == 'en' && !empty($data['branch_name_en'])) ? $data['branch_name_en'] : (!empty($data['branch_name_ar']) ? $data['branch_name_ar'] : '---');

            $journalNo = $data["journalNo"] ?? '';										
            $disc      = $data["disc"] ?? '';
            $credit    = floatval($data["credit"] ?? 0);
            $debit     = floatval($data["debit"] ?? 0);                                                                                
            $date      = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : '---';
            
            $newBalanceDebit  = $oldBalanceDebit - $oldBalanceCredit + $debit - $credit;
            $newBalanceCredit = $oldBalanceCredit - $oldBalanceDebit + $credit - $debit; 
            if ($newBalanceDebit <= 0)  $newBalanceDebit = 0;
            if ($newBalanceCredit <= 0) $newBalanceCredit = 0;

            $totalDebit  += $debit;
            $totalCredit += $credit;

            $ledgerRows[] = [
                'seq'           => $seq,
                'date'          => $date,
                'journalNo'     => $journalNo,
                'accName'       => $accName,
                'accCode'       => $data['accCode'],
                'branch'        => $branchName,
                'disc'          => $disc,
                'debit'         => $debit,
                'credit'        => $credit,
                'balanceDebit'  => $newBalanceDebit,
                'balanceCredit' => $newBalanceCredit
            ];

            $oldBalanceDebit  = $newBalanceDebit;
            $oldBalanceCredit = $newBalanceCredit;
        }
    }

    $balanceDebit  = $totalDebit - $totalCredit;
    $balanceCredit = $totalCredit - $totalDebit; 
    if ($balanceDebit <= 0)  $balanceDebit = 0;
    if ($balanceCredit <= 0) $balanceCredit = 0;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حساب الأستاذ التفصيلي | WAM Tech ERP</title>
    <link rel="stylesheet" href="css/admin-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
    </style>
</head>
<body class="admin-pro-body">
    <?php require_once("components/header_pro.php"); ?>
    
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>
        
        <main class="admin-main">
            <div class="container-fluid px-4 py-4">
                
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-file-invoice text-primary me-2"></i> كشف حساب الأستاذ العام
                        </h2>
                        <p class="text-muted mb-0">كشف تفصيلي لحركات الحسابات المالية، المجاميع، والأرصدة المدينة والدائنة</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="accounts.php" class="btn btn-outline-secondary">
                            <i class="fas fa-sitemap me-1"></i> شجرة الحسابات
                        </a>
                        <a href="journal.php" class="btn btn-outline-primary">
                            <i class="fas fa-book me-1"></i> دفتر اليومية
                        </a>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="<?= $pgName ?>" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-calculator me-1"></i> الحساب المالي
                                </label>
                                <select name="account" class="form-select select2-basic">
                                    <option value="0">-- كافة الحسابات --</option>
                                    <?php foreach ($accountsList as $accItem): 
                                        $sel = ($selectedAcc !== '' && $selectedAcc == $accItem['code']) ? 'selected' : '';
                                    ?>
                                        <option value="<?= htmlspecialchars($accItem['code']) ?>" <?= $sel ?>><?= htmlspecialchars($accItem['label']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-code-branch me-1"></i> الفرع
                                </label>
                                <select name="branch" class="form-select select2-basic">
                                    <option value="">-- كافة الفروع --</option>
                                    <?php foreach ($branchesList as $b): 
                                        $bName = $b['name_' . $language] ?? $b['name_ar'];
                                        $sel = ($selectedBranch !== '' && $selectedBranch == $b['id']) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $b['id'] ?>" <?= $sel ?>><?= htmlspecialchars($bName) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-calendar-alt me-1"></i> من تاريخ
                                </label>
                                <input type="date" name="fr_date" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-calendar-check me-1"></i> إلى تاريخ
                                </label>
                                <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
                            </div>

                            <div class="col-12 d-flex gap-2 justify-content-end pt-2">
                                <button type="submit" name="Submit" value="1" class="btn btn-primary px-4">
                                    <i class="fas fa-search me-1"></i> استعراض كشف الحساب
                                </button>
                                <a href="<?= $pgName ?>" class="btn btn-outline-secondary px-3" title="إعادة ضبط">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($isSubmitted): ?>

                    <!-- KPI Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">إجمالي الحركات</div>
                                    <div class="fs-4 fw-bold text-dark font-monospace"><?= count($ledgerRows) ?></div>
                                </div>
                                <div class="kpi-icon" style="background:#f1f5f9; color:#475569;">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">إجمالي المدين (حركة)</div>
                                    <div class="fs-4 fw-bold text-success font-monospace"><?= number_format($totalDebit, 2) ?> <small class="fs-6 text-muted">ج.م</small></div>
                                </div>
                                <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">إجمالي الدائن (حركة)</div>
                                    <div class="fs-4 fw-bold text-danger font-monospace"><?= number_format($totalCredit, 2) ?> <small class="fs-6 text-muted">ج.م</small></div>
                                </div>
                                <div class="kpi-icon" style="background:#fee2e2; color:#dc2626;">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">الرصيد الصافي النهائي</div>
                                    <?php if ($balanceDebit > 0): ?>
                                        <div class="fs-4 fw-bold text-success font-monospace"><?= number_format($balanceDebit, 2) ?> <small class="fs-6">مدين</small></div>
                                    <?php elseif ($balanceCredit > 0): ?>
                                        <div class="fs-4 fw-bold text-danger font-monospace"><?= number_format($balanceCredit, 2) ?> <small class="fs-6">دائن</small></div>
                                    <?php else: ?>
                                        <div class="fs-4 fw-bold text-muted font-monospace">0.00 <small class="fs-6">متزن</small></div>
                                    <?php endif; ?>
                                </div>
                                <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ledger Table Card -->
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-list-check text-secondary me-2"></i> سجل حركات الأستاذ التفصيلية
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table id="ledgerTable" class="table table-hover align-middle table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 45px;">#</th>
                                            <th>التاريخ</th>
                                            <th>رقم القيد</th>
                                            <th>اسم الحساب</th>
                                            <th>الفرع</th>
                                            <th>البيان / الشرح</th>
                                            <th class="text-end text-success">مدين (+)</th>
                                            <th class="text-end text-danger">دائن (-)</th>
                                            <th class="text-end text-success bg-light">رصيد مدين</th>
                                            <th class="text-end text-danger bg-light">رصيد دائن</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ledgerRows as $row): ?>
                                            <tr>
                                                <td class="text-center text-muted"><?= $row['seq'] ?></td>
                                                <td class="font-monospace text-secondary small"><?= htmlspecialchars($row['date']) ?></td>
                                                <td>
                                                    <a href="edit_journal.php?no=<?= urlencode($row['journalNo']) ?>" class="badge bg-light text-primary border font-monospace px-2 py-1 fs-6 text-decoration-none">
                                                        <?= htmlspecialchars($row['journalNo']) ?>
                                                    </a>
                                                </td>
                                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['accName']) ?></td>
                                                <td><span class="text-secondary"><?= htmlspecialchars($row['branch']) ?></span></td>
                                                <td class="small"><?= htmlspecialchars($row['disc'] ?: '---') ?></td>
                                                <td class="text-end font-monospace fw-bold text-success">
                                                    <?= $row['debit'] > 0 ? number_format($row['debit'], 2) : '-' ?>
                                                </td>
                                                <td class="text-end font-monospace fw-bold text-danger">
                                                    <?= $row['credit'] > 0 ? number_format($row['credit'], 2) : '-' ?>
                                                </td>
                                                <td class="text-end font-monospace fw-bold text-success bg-light">
                                                    <?= $row['balanceDebit'] > 0 ? number_format($row['balanceDebit'], 2) : '0.00' ?>
                                                </td>
                                                <td class="text-end font-monospace fw-bold text-danger bg-light">
                                                    <?= $row['balanceCredit'] > 0 ? number_format($row['balanceCredit'], 2) : '0.00' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="fw-bold">
                                            <td colspan="6" class="text-end">الإجماليات:</td>
                                            <td class="text-end text-success font-monospace"><?= number_format($totalDebit, 2) ?></td>
                                            <td class="text-end text-danger font-monospace"><?= number_format($totalCredit, 2) ?></td>
                                            <td class="text-end text-success font-monospace"><?= number_format($balanceDebit, 2) ?></td>
                                            <td class="text-end text-danger font-monospace"><?= number_format($balanceCredit, 2) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-3 text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-file-invoice text-muted fs-1 mb-3"></i>
                            <h4 class="fw-bold text-dark">يرجى تحديد المعايير لعرض كشف الحساب</h4>
                            <p class="text-muted mb-0">اختر الحساب المالي أو الفرع والفترة الزمنية من النموذج أعلاه ثم اضغط "استعراض كشف الحساب".</p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2-basic').select2({ dir: 'rtl', width: '100%' });

            if ($('#ledgerTable').length) {
                $('#ledgerTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
                    },
                    dom: "<'row mb-3'<'col-md-6 d-flex align-items-center gap-2'B><'col-md-6'f>>" +
                         "<'row'<'col-12'tr>>" +
                         "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel text-success me-1"></i> تصدير إكسيل',
                            className: 'btn btn-outline-success btn-sm'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print me-1"></i> طباعة كشف الحساب',
                            className: 'btn btn-outline-dark btn-sm'
                        }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 25
                });
            }
        });
    </script>
</body>
</html>
