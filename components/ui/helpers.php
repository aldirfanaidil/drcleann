<?php
/**
 * Component Helper Functions
 * 
 * Fungsi untuk memudahkan penggunaan komponen UI
 */

/**
 * Menampilkan loader sederhana
 * 
 * @param string $size 'sm', 'md', 'lg'
 * @param string $color 'primary', 'blue', 'red', 'green'
 */
function loader($size = 'md', $color = 'primary') {
    global $size, $color;
    include __DIR__ . '/loader.php';
}

/**
 * Menampilkan concentric loader
 * 
 * @param string $size 'sm', 'md', 'lg'
 * @param string $outerColor 'blue', 'red', 'green', 'primary'
 * @param string $innerColor 'red', 'blue', 'green', 'primary'
 * @param string $text Teks yang ditampilkan di bawah loader
 */
function concentric_loader($size = 'md', $outerColor = 'blue', $innerColor = 'red', $text = '') {
    global $size, $outerColor, $innerColor, $text;
    include __DIR__ . '/concentric-loader.php';
}

/**
 * Menampilkan loading overlay dengan loader
 * 
 * @param string $message Pesan loading
 * @param string $loaderType 'simple' atau 'concentric'
 */
function loading_overlay($message = 'Loading...', $loaderType = 'concentric') {
    ?>
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
            <div class="flex flex-col items-center gap-4">
                <?php if ($loaderType === 'concentric'): ?>
                    <?php concentric_loader('md', 'blue', 'red'); ?>
                <?php else: ?>
                    <?php loader('md', 'primary'); ?>
                <?php endif; ?>
                <p class="text-gray-700 text-center"><?php echo htmlspecialchars($message); ?></p>
            </div>
        </div>
    </div>
    <?php
}
?>