<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contoh Komponen Loader - Dr. ShoezClean</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Contoh Komponen Loader</h1>
        
        <!-- Simple Loader Examples -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Simple Loader</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Small - Primary</h3>
                    <?php $size = 'sm'; $color = 'primary'; include 'components/ui/loader.php'; ?>
                </div>
                
                <div class="text-center">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Medium - Blue</h3>
                    <?php $size = 'md'; $color = 'blue'; include 'components/ui/loader.php'; ?>
                </div>
                
                <div class="text-center">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Large - Green</h3>
                    <?php $size = 'lg'; $color = 'green'; include 'components/ui/loader.php'; ?>
                </div>
            </div>
        </div>
        
        <!-- Concentric Loader Examples -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Concentric Loader</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="text-center">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Blue-Red dengan Teks</h3>
                    <?php 
                    $size = 'md'; 
                    $outerColor = 'blue'; 
                    $innerColor = 'red'; 
                    $text = 'Memproses data...';
                    include 'components/ui/concentric-loader.php'; 
                    ?>
                </div>
                
                <div class="text-center">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Green-Primary Large</h3>
                    <?php 
                    $size = 'lg'; 
                    $outerColor = 'green'; 
                    $innerColor = 'primary'; 
                    $text = '';
                    include 'components/ui/concentric-loader.php'; 
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Interactive Examples -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Contoh Penggunaan</h2>
            
            <div class="space-y-4">
                <button onclick="showLoading()" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition-colors">
                    Tampilkan Loading Overlay
                </button>
                
                <div id="loadingContainer" class="hidden">
                    <?php 
                    $message = 'Sedang menyimpan data...';
                    $loaderType = 'concentric';
                    include 'components/ui/helpers.php';
                    loading_overlay($message, $loaderType);
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Integration Guide -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Cara Penggunaan</h2>
            
            <div class="space-y-4 text-sm">
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">1. Simple Loader:</h3>
                    <code class="block bg-gray-100 p-2 rounded">
                        &lt;?php $size = 'md'; $color = 'primary'; include 'components/ui/loader.php'; ?&gt;
                    </code>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">2. Concentric Loader:</h3>
                    <code class="block bg-gray-100 p-2 rounded">
                        &lt;?php 
                        $size = 'md'; 
                        $outerColor = 'blue'; 
                        $innerColor = 'red'; 
                        $text = 'Loading...';
                        include 'components/ui/concentric-loader.php'; 
                        ?&gt;
                    </code>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">3. Menggunakan Helper Functions:</h3>
                    <code class="block bg-gray-100 p-2 rounded">
                        &lt;?php 
                        include 'components/ui/helpers.php';
                        loader('lg', 'green');
                        concentric_loader('md', 'blue', 'red', 'Memproses...');
                        ?&gt;
                    </code>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showLoading() {
            const container = document.getElementById('loadingContainer');
            container.classList.remove('hidden');
            
            setTimeout(() => {
                container.classList.add('hidden');
            }, 3000);
        }
    </script>
</body>
</html>