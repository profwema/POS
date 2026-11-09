<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * كلاس محرك القوالب وعرض الواجهات (View & Template Engine)
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

class View {

    /**
     * عرض قالب صفحة كاملة داخل إطار تصميم (Layout)
     * 
     * @param string $view مسار ملف العرض داخل views (مثال: 'modules/dashboard/index')
     * @param array $data مصفوفة المتغيرات الممررة للقالب
     * @param string|false $layout اسم ملف الإطار داخل views/layouts (أو false بدون إطار)
     */
    public static function render(string $view, array $data = [], string|false $layout = 'main'): void {
        // استخراج المتغيرات لتكون متاحة بأسماء مباشرة داخل القالب
        extract($data, EXTR_SKIP);

        $viewFile = VIEWS_PATH . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $view) . '.php';

        if (!file_exists($viewFile)) {
            die("خطأ: تعذر العثور على ملف القالب المطلوب: " . htmlspecialchars($view));
        }

        // التقاط مخرجات القالب الداخلي (Buffer Content)
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // إذا كان هناك إطار مطلوب، نضمنه ونمرر له المحتوى المُلتقط
        if ($layout !== false) {
            $layoutFile = VIEWS_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
                return;
            }
        }

        // إذا لم يكن هناك إطار، نطبع المحتوى مباشرة
        echo $content;
    }

    /**
     * تضمين جزء فرعي مشترك (Component)
     */
    public static function component(string $name, array $data = []): void {
        extract($data, EXTR_SKIP);
        $file = VIEWS_PATH . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $name . '.php';
        if (file_exists($file)) {
            include $file;
        }
    }
}

