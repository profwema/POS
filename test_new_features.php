<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>اختبار الميزات الجديدة - WAM Tech Soft</title>
	
	<!-- CSS Files -->
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/modern-theme.css" rel="stylesheet">
	<link href="css/dark-mode.css" rel="stylesheet">
	
	<style>
		body {
			padding: 2rem;
			background: #f8fafc;
		}
		.test-container {
			max-width: 1200px;
			margin: 0 auto;
		}
		.feature-card {
			background: white;
			padding: 2rem;
			border-radius: 1rem;
			margin-bottom: 2rem;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		.feature-card h2 {
			margin: 0 0 1rem;
			color: #1e293b;
			font-size: 1.5rem;
		}
		.feature-card p {
			color: #64748b;
			margin-bottom: 1.5rem;
		}
		.demo-btn {
			display: inline-block;
			padding: 0.75rem 1.5rem;
			background: #6366f1;
			color: white;
			border: none;
			border-radius: 0.5rem;
			cursor: pointer;
			font-weight: 600;
			margin: 0.5rem 0.5rem 0 0;
			transition: all 0.3s;
		}
		.demo-btn:hover {
			background: #4f46e5;
			transform: translateY(-2px);
		}
		.demo-btn.success {
			background: #10b981;
		}
		.demo-btn.warning {
			background: #f59e0b;
		}
		.demo-btn.danger {
			background: #ef4444;
		}
		.shortcut-key {
			display: inline-block;
			padding: 0.25rem 0.5rem;
			background: #e2e8f0;
			color: #475569;
			border-radius: 0.25rem;
			font-family: monospace;
			font-size: 0.875rem;
			margin: 0 0.25rem;
		}
		.test-form {
			margin-top: 1rem;
		}
		.test-form input,
		.test-form textarea {
			width: 100%;
			padding: 0.75rem;
			margin-bottom: 1rem;
			border: 1px solid #e2e8f0;
			border-radius: 0.5rem;
			font-size: 1rem;
		}
		.header-banner {
			background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
			color: white;
			padding: 3rem;
			border-radius: 1rem;
			text-align: center;
			margin-bottom: 2rem;
		}
		.header-banner h1 {
			margin: 0 0 0.5rem;
			font-size: 2.5rem;
		}
		.header-banner p {
			margin: 0;
			opacity: 0.9;
			color: white !important;
		}
		.shortcuts-list {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 1rem;
			margin-top: 1rem;
		}
		.shortcut-item {
			padding: 1rem;
			background: #f8fafc;
			border-radius: 0.5rem;
			border-right: 3px solid #6366f1;
		}
		.shortcut-item strong {
			display: block;
			margin-bottom: 0.25rem;
			color: #1e293b;
		}
		.shortcut-item span {
			color: #64748b;
			font-size: 0.875rem;
		}
		[data-theme="dark"] body {
			background: #0f172a;
		}
		[data-theme="dark"] .feature-card {
			background: #1e293b;
			border: 1px solid #334155;
		}
		[data-theme="dark"] .feature-card h2 {
			color: #f1f5f9;
		}
		[data-theme="dark"] .feature-card p {
			color: #cbd5e1;
		}
		[data-theme="dark"] .shortcut-key {
			background: #334155;
			color: #e2e8f0;
		}
		[data-theme="dark"] .shortcut-item {
			background: #0f172a;
		}
	</style>
</head>
<body>

<div class="test-container">
	
	<!-- Header -->
	<div class="header-banner">
		<h1>🎉 اختبار الميزات الجديدة</h1>
		<p>WAM Tech Soft POS System v2.1</p>
	</div>

	<!-- Feature 1: Dark Mode -->
	<div class="feature-card">
		<h2>🌙 Dark Mode (الوضع الداكن)</h2>
		<p>جرب تبديل الوضع الداكن بطريقتين مختلفتين:</p>
		
		<button class="demo-btn" id="darkModeBtn">
			<i class="fa fa-moon-o"></i> تبديل الوضع الداكن
		</button>
		
		<p style="margin-top: 1rem;">
			<strong>الطريقة السريعة:</strong> اضغط 
			<span class="shortcut-key">Ctrl</span> + 
			<span class="shortcut-key">Shift</span> + 
			<span class="shortcut-key">D</span>
		</p>
		
		<p>
			<strong>الحالة الحالية:</strong> 
			<span id="themeStatus" style="font-weight: 600; color: #6366f1;">وضع فاتح</span>
		</p>
	</div>

	<!-- Feature 2: Keyboard Shortcuts -->
	<div class="feature-card">
		<h2>⌨️ اختصارات لوحة المفاتيح</h2>
		<p>جرب الاختصارات التالية:</p>
		
		<div class="shortcuts-list">
			<div class="shortcut-item">
				<strong>Ctrl + /</strong>
				<span>عرض قائمة الاختصارات</span>
			</div>
			<div class="shortcut-item">
				<strong>Ctrl + S</strong>
				<span>حفظ النموذج</span>
			</div>
			<div class="shortcut-item">
				<strong>Ctrl + K</strong>
				<span>بحث سريع</span>
			</div>
			<div class="shortcut-item">
				<strong>Ctrl + P</strong>
				<span>طباعة</span>
			</div>
			<div class="shortcut-item">
				<strong>ESC</strong>
				<span>إغلاق النوافذ</span>
			</div>
			<div class="shortcut-item">
				<strong>F2</strong>
				<span>تعديل</span>
			</div>
		</div>
		
		<button class="demo-btn" onclick="showNotification('تم اختبار الإشعار بنجاح! 🎉', 'success')">
			<i class="fa fa-bell"></i> اختبر الإشعارات
		</button>
		
		<button class="demo-btn warning" onclick="showNotification('هذا تحذير اختباري!', 'warning')">
			<i class="fa fa-exclamation-triangle"></i> إشعار تحذير
		</button>
		
		<button class="demo-btn danger" onclick="showNotification('هذا خطأ اختباري!', 'error')">
			<i class="fa fa-times-circle"></i> إشعار خطأ
		</button>
	</div>

	<!-- Feature 3: Auto-save -->
	<div class="feature-card">
		<h2>💾 Auto-save (الحفظ التلقائي)</h2>
		<p>اكتب شيئاً في النموذج أدناه ثم غادر الصفحة وعد مرة أخرى. ستجد بياناتك محفوظة!</p>
		
		<form id="testForm" data-autosave data-autosave-interval="10000" class="test-form">
			<input type="text" name="test_name" placeholder="اسمك (سيُحفظ تلقائياً)">
			<input type="email" name="test_email" placeholder="بريدك الإلكتروني">
			<textarea name="test_message" rows="4" placeholder="رسالتك (جرب الكتابة ثم أعد تحميل الصفحة)"></textarea>
			
			<button type="button" class="demo-btn" onclick="showNotification('تم حفظ البيانات يدوياً!', 'success')">
				<i class="fa fa-save"></i> حفظ يدوي
			</button>
			
			<button type="button" class="demo-btn danger" onclick="clearForm()">
				<i class="fa fa-trash"></i> مسح النموذج
			</button>
		</form>
		
		<p style="margin-top: 1rem;">
			<i class="fa fa-info-circle" style="color: #3b82f6;"></i>
			<strong>ملاحظة:</strong> سيتم الحفظ تلقائياً كل 10 ثواني أو عند ترك الحقل
		</p>
	</div>

	<!-- Feature 4: Loading States -->
	<div class="feature-card">
		<h2>⏳ حالات التحميل</h2>
		<p>اختبر حالات التحميل والأزرار:</p>
		
		<button class="demo-btn" id="loadingTestBtn" onclick="testLoading()">
			<i class="fa fa-spinner"></i> اختبر التحميل (3 ثوانٍ)
		</button>
		
		<button class="demo-btn success" id="buttonLoadingTest" onclick="testButtonLoading()">
			<i class="fa fa-check"></i> اختبر تحميل الزر
		</button>
	</div>

	<!-- Navigation to Main System -->
	<div class="feature-card" style="text-align: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
		<h2 style="color: white; margin-bottom: 0.5rem;">✅ جاهز للبدء؟</h2>
		<p style="color: white !important; opacity: 0.9;">انتقل إلى النظام الرئيسي لرؤية جميع التحسينات</p>
		<a href="index.php" class="demo-btn" style="background: white; color: #10b981;">
			<i class="fa fa-home"></i> الذهاب إلى النظام
		</a>
	</div>

</div>

<!-- JavaScript -->
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/modern-ui.js"></script>
<script src="js/dark-mode.js"></script>
<script src="js/keyboard-shortcuts.js"></script>
<script src="js/auto-save.js"></script>

<script>
// Dark Mode Toggle Button
$('#darkModeBtn').on('click', function() {
	toggleTheme();
	updateThemeStatus();
});

// Update theme status
function updateThemeStatus() {
	const theme = document.documentElement.getAttribute('data-theme');
	const status = theme === 'dark' ? 'وضع داكن 🌙' : 'وضع فاتح ☀️';
	const color = theme === 'dark' ? '#a5b4fc' : '#6366f1';
	$('#themeStatus').text(status).css('color', color);
}

// Watch for theme changes
const observer = new MutationObserver(updateThemeStatus);
observer.observe(document.documentElement, {
	attributes: true,
	attributeFilter: ['data-theme']
});

// Test Loading
function testLoading() {
	showLoading('جاري التحميل...');
	setTimeout(function() {
		hideLoading();
		showNotification('اكتمل التحميل بنجاح!', 'success');
	}, 3000);
}

// Test Button Loading
function testButtonLoading() {
	const btn = $('#buttonLoadingTest');
	
	// Set loading state
	btn.prop('disabled', true)
	   .html('<i class="fa fa-spinner fa-spin"></i> جاري التحميل...');
	
	setTimeout(function() {
		btn.prop('disabled', false)
		   .html('<i class="fa fa-check"></i> تم بنجاح!');
		
		setTimeout(function() {
			btn.html('<i class="fa fa-check"></i> اختبر تحميل الزر');
		}, 2000);
	}, 2000);
}

// Clear Form
function clearForm() {
	if (confirm('هل أنت متأكد من مسح جميع البيانات؟')) {
		$('#testForm')[0].reset();
		localStorage.removeItem('autosave_testForm');
		showNotification('تم مسح النموذج والبيانات المحفوظة', 'success');
	}
}

// Initialize
$(document).ready(function() {
	updateThemeStatus();
	
	// Welcome notification
	setTimeout(function() {
		showNotification('مرحباً بك في صفحة اختبار الميزات الجديدة! 🎉', 'info', 5000);
	}, 500);
});
</script>

</body>
</html>

