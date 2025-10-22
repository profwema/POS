<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language         = $_SESSION['lang'];
$storeid        = $_SESSION['storeid'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WAM Tech POS - <?= SHIFTS ?></title>

        <?php require_once("header.php"); ?>
        <link href="css/admin-pro.css" rel="stylesheet">
</head>

<body>

        <div class="admin-wrapper">

                <!-- Professional Sidebar -->
                <?php require_once("components/sidebar_pro.php"); ?>

                <!-- Main Content Wrapper -->
                <div style="flex: 1; display: flex; flex-direction: column;">

                        <!-- Professional Navbar -->
                        <?php require_once("components/header_pro.php"); ?>

                        <!-- Main Content -->
                        <main class="admin-content">

                                <!-- Page Header -->
                                <div class="page-header">
                                        <h1 class="page-title">
                                                <i class="fas fa-clock text-primary me-2"></i>
                                                <?= SHIFTS ?>
                                        </h1>
                                        <div class="page-breadcrumb">
                                                <div class="breadcrumb-item">
                                                        <i class="fas fa-home"></i>
                                                        <span>الرئيسية</span>
                                                </div>
                                                <span class="breadcrumb-separator">/</span>
                                                <div class="breadcrumb-item">
                                                        <span><?= FILES ?></span>
                                                </div>
                                                <span class="breadcrumb-separator">/</span>
                                                <div class="breadcrumb-item">
                                                        <span class="text-primary"><?= SHIFTS ?></span>
                                                </div>
                                        </div>
                                </div>

                                <!-- Shifts Card -->
                                <div class="card-pro">
                                        <div class="card-header-pro">
                                                <h2 class="card-title-pro">
                                                        <i class="fas fa-edit"></i>
                                                        تحديث أوقات الدوام
                                                </h2>
                                        </div>
                                        <div class="card-body-pro">
                                                <form id="update-shifts">
                                                        <input type="hidden" value="shiftsUpdate" name="f">

                                                        <?php
                                                        $query = "SELECT * FROM shifts WHERE storeid='$storeid'";
                                                        $res = mysqli_query($adController->MySQL, $query);
                                                        do {
                                                                $_REQUEST['from_time'] = $dataCat['starts'];
                                                                $_REQUEST['to_time']   = $dataCat['ends'];
                                                                $id = $dataCat['id'];
                                                                $name = $dataCat['name_' . $language];
                                                        ?>

                                                                <div class="form-group-pro mb-4">
                                                                        <div class="row g-3">
                                                                                <input type="hidden" value='<?= $dataCat['id'] ?>' name='id[]' />

                                                                                <div class="col-md-3">
                                                                                        <input type="text" class="form-input-pro" name='name_en[]' value="<?= $dataCat['name_en'] ?>" placeholder='English name'>
                                                                                </div>

                                                                                <div class="col-md-3">
                                                                                        <input type="text" class="form-input-pro" name='name_ar[]' value="<?= $dataCat['name_ar'] ?>" placeholder='الاسم بالعربي'>
                                                                                </div>

                                                                                <div class="col-md-3">
                                                                                        <select class='form-input-pro' name="from_time[]">
                                                                                                <option value="0" <?php if ($_REQUEST['from_time'] == "0") echo "selected='true'"; ?>>12:00 AM</option>
                                                                                                <option value="1" <?php if ($_REQUEST['from_time'] == "1") echo "selected='true'"; ?>>1:00 AM</option>
                                                                                                <option value="2" <?php if ($_REQUEST['from_time'] == "2") echo "selected='true'"; ?>>2:00 AM</option>
                                                                                                <option value="3" <?php if ($_REQUEST['from_time'] == "3") echo "selected='true'"; ?>>3:00 AM</option>
                                                                                                <option value="4" <?php if ($_REQUEST['from_time'] == "4") echo "selected='true'"; ?>>4:00 AM</option>
                                                                                                <option value="5" <?php if ($_REQUEST['from_time'] == "5") echo "selected='true'"; ?>>5:00 AM</option>
                                                                                                <option value="6" <?php if ($_REQUEST['from_time'] == "6") echo "selected='true'"; ?>>6:00 AM</option>
                                                                                                <option value="7" <?php if ($_REQUEST['from_time'] == "7") echo "selected='true'"; ?>>7:00 AM</option>
                                                                                                <option value="8" <?php if ($_REQUEST['from_time'] == "8") echo "selected='true'"; ?>>8:00 AM</option>
                                                                                                <option value="9" <?php if ($_REQUEST['from_time'] == "9") echo "selected='true'"; ?>>9:00 AM</option>
                                                                                                <option value="10" <?php if ($_REQUEST['from_time'] == "10") echo "selected='true'"; ?>>10:00 AM</option>
                                                                                                <option value="11" <?php if ($_REQUEST['from_time'] == "11") echo "selected='true'"; ?>>11:00 AM</option>
                                                                                                <option value="12" <?php if ($_REQUEST['from_time'] == "12") echo "selected='true'"; ?>>12:00 PM</option>
                                                                                                <option value="13" <?php if ($_REQUEST['from_time'] == "13") echo "selected='true'"; ?>>1:00 PM</option>
                                                                                                <option value="14" <?php if ($_REQUEST['from_time'] == "14") echo "selected='true'"; ?>>2:00 PM</option>
                                                                                                <option value="15" <?php if ($_REQUEST['from_time'] == "15") echo "selected='true'"; ?>>3:00 PM</option>
                                                                                                <option value="16" <?php if ($_REQUEST['from_time'] == "16") echo "selected='true'"; ?>>4:00 PM</option>
                                                                                                <option value="17" <?php if ($_REQUEST['from_time'] == "17") echo "selected='true'"; ?>>5:00 PM</option>
                                                                                                <option value="18" <?php if ($_REQUEST['from_time'] == "18") echo "selected='true'"; ?>>6:00 PM</option>
                                                                                                <option value="19" <?php if ($_REQUEST['from_time'] == "19") echo "selected='true'"; ?>>7:00 PM</option>
                                                                                                <option value="20" <?php if ($_REQUEST['from_time'] == "20") echo "selected='true'"; ?>>8:00 PM</option>
                                                                                                <option value="21" <?php if ($_REQUEST['from_time'] == "21") echo "selected='true'"; ?>>9:00 PM</option>
                                                                                                <option value="22" <?php if ($_REQUEST['from_time'] == "22") echo "selected='true'"; ?>>10:00 PM</option>
                                                                                                <option value="23" <?php if ($_REQUEST['from_time'] == "23") echo "selected='true'"; ?>>11:00 PM</option>
                                                                                        </select>
                                                                                </div>

                                                                                <div class="col-md-3">
                                                                                        <select class='form-input-pro' name="to_time[]">
                                                                                                <option value="0" <?php if ($_REQUEST['to_time'] == "0") echo "selected='true'"; ?>>12:00 AM</option>
                                                                                                <option value="1" <?php if ($_REQUEST['to_time'] == "1") echo "selected='true'"; ?>>1:00 AM</option>
                                                                                                <option value="2" <?php if ($_REQUEST['to_time'] == "2") echo "selected='true'"; ?>>2:00 AM</option>
                                                                                                <option value="3" <?php if ($_REQUEST['to_time'] == "3") echo "selected='true'"; ?>>3:00 AM</option>
                                                                                                <option value="4" <?php if ($_REQUEST['to_time'] == "4") echo "selected='true'"; ?>>4:00 AM</option>
                                                                                                <option value="5" <?php if ($_REQUEST['to_time'] == "5") echo "selected='true'"; ?>>5:00 AM</option>
                                                                                                <option value="6" <?php if ($_REQUEST['to_time'] == "6") echo "selected='true'"; ?>>6:00 AM</option>
                                                                                                <option value="7" <?php if ($_REQUEST['to_time'] == "7") echo "selected='true'"; ?>>7:00 AM</option>
                                                                                                <option value="8" <?php if ($_REQUEST['to_time'] == "8") echo "selected='true'"; ?>>8:00 AM</option>
                                                                                                <option value="9" <?php if ($_REQUEST['to_time'] == "9") echo "selected='true'"; ?>>9:00 AM</option>
                                                                                                <option value="10" <?php if ($_REQUEST['to_time'] == "10") echo "selected='true'"; ?>>10:00 AM</option>
                                                                                                <option value="11" <?php if ($_REQUEST['to_time'] == "11") echo "selected='true'"; ?>>11:00 AM</option>
                                                                                                <option value="12" <?php if ($_REQUEST['to_time'] == "12") echo "selected='true'"; ?>>12:00 PM</option>
                                                                                                <option value="13" <?php if ($_REQUEST['to_time'] == "13") echo "selected='true'"; ?>>1:00 PM</option>
                                                                                                <option value="14" <?php if ($_REQUEST['to_time'] == "14") echo "selected='true'"; ?>>2:00 PM</option>
                                                                                                <option value="15" <?php if ($_REQUEST['to_time'] == "15") echo "selected='true'"; ?>>3:00 PM</option>
                                                                                                <option value="16" <?php if ($_REQUEST['to_time'] == "16") echo "selected='true'"; ?>>4:00 PM</option>
                                                                                                <option value="17" <?php if ($_REQUEST['to_time'] == "17") echo "selected='true'"; ?>>5:00 PM</option>
                                                                                                <option value="18" <?php if ($_REQUEST['to_time'] == "18") echo "selected='true'"; ?>>6:00 PM</option>
                                                                                                <option value="19" <?php if ($_REQUEST['to_time'] == "19") echo "selected='true'"; ?>>7:00 PM</option>
                                                                                                <option value="20" <?php if ($_REQUEST['to_time'] == "20") echo "selected='true'"; ?>>8:00 PM</option>
                                                                                                <option value="21" <?php if ($_REQUEST['to_time'] == "21") echo "selected='true'"; ?>>9:00 PM</option>
                                                                                                <option value="22" <?php if ($_REQUEST['to_time'] == "22") echo "selected='true'"; ?>>10:00 PM</option>
                                                                                                <option value="23" <?php if ($_REQUEST['to_time'] == "23") echo "selected='true'"; ?>>11:00 PM</option>
                                                                                        </select>
                                                                                </div>
                                                                        </div>
                                                                </div>

                                                        <?php
                                                        } while ($dataCat = mysqli_fetch_assoc($res));
                                                        ?>

                                                        <div class="d-flex gap-2 mt-4">
                                                                <button type="button" id='submit-shifts' class="btn-pro btn-pro-primary">
                                                                        <i class="fas fa-save me-1"></i>
                                                                        <?= SAVE ?>
                                                                </button>
                                                                <button type="reset" class="btn-pro btn-pro-outline">
                                                                        <i class="fas fa-times me-1"></i>
                                                                        <?= CANCEL ?>
                                                                </button>
                                                        </div>

                                                        <p class='error-red mt-3'>&nbsp;</p>

                                                </form>

                                        </div>
                                </div>

                                <!-- Professional Footer -->
                                <?php require_once("components/footer_pro.php"); ?>

                        </main>

                </div>

        </div>

        <!-- Scripts -->
        <?php require_once("include.php"); ?>
        <script src="js/admin-pro.js"></script>

</body>

</html>