<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODERN ADMIN RTL - TOP NAVIGATION BAR -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm" dir="rtl">
    <div class="container-fluid">

        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href=".">
            <img src="img/pos1.png" alt="Logo" height="32" class="d-inline-block">
            <span class="fw-bold">WAM Tech Soft</span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">

                <!-- POS Button -->
                <li class="nav-item">
                    <a class="nav-link btn btn-sm btn-success text-white px-3" href="pos.php" title="POS">
                        <i class="fas fa-cash-register me-1"></i>
                        <span>شاشة الكاشير</span>
                    </a>
                </li>

                <!-- Language Switcher -->
                <li class="nav-item">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-light" onclick="changeLang('ar')">
                            عربي
                        </button>
                        <button type="button" class="btn btn-outline-light" onclick="changeLang('en')">
                            EN
                        </button>
                    </div>
                </li>

                <!-- Dark Mode Toggle -->
                <li class="nav-item">
                    <button class="btn btn-sm btn-outline-light" id="themeToggle" title="Dark Mode">
                        <i id="themeIcon" class="fas fa-moon"></i>
                    </button>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                        id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="img/emp/user.png" alt="User" width="32" height="32" class="rounded-circle">
                        <div class="d-flex flex-column text-end">
                            <small class="text-white-50" style="font-size: 0.7rem;"><?= WELCOME ?></small>
                            <span class="text-white fw-bold" style="font-size: 0.85rem;"><?= $_SESSION['name'] ?></span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li>
                            <span class="dropdown-item text-danger" id="logout" style="cursor:pointer">
                                <i class="fas fa-power-off me-2"></i>
                                <?= LOGOUT ?>
                            </span>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>