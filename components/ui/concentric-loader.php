<?php
/**
 * Concentric Loader Component
 * 
 * Usage:
 * include 'components/ui/concentric-loader.php';
 * 
 * Optional parameters:
 * - $size: 'sm', 'md', 'lg' (default: 'md')
 * - $outerColor: 'blue', 'red', 'green', 'primary' (default: 'blue')
 * - $innerColor: 'red', 'blue', 'green', 'primary' (default: 'red')
 * - $text: Optional text to display below loader
 */

// Default values
$size = $size ?? 'md';
$outerColor = $outerColor ?? 'blue';
$innerColor = $innerColor ?? 'red';
$text = $text ?? '';

// Size configurations
$sizes = [
    'sm' => ['outer' => 'h-8 w-8', 'inner' => 'h-6 w-6'],
    'md' => ['outer' => 'h-16 w-16', 'inner' => 'h-12 w-12'],
    'lg' => ['outer' => 'h-24 w-24', 'inner' => 'h-20 w-20']
];

// Color classes
$colors = [
    'blue' => 'border-blue-400',
    'red' => 'border-red-400', 
    'green' => 'border-green-400',
    'primary' => 'border-primary'
];

$sizeConfig = $sizes[$size] ?? $sizes['md'];
$outerColorClass = $colors[$outerColor] ?? $colors['blue'];
$innerColorClass = $colors[$innerColor] ?? $colors['red'];
?>

<div class="flex w-full flex-col items-center justify-center gap-4">
  <div class="flex <?php echo $sizeConfig['outer']; ?> animate-spin items-center justify-center rounded-full border-4 border-transparent <?php echo $outerColorClass; ?>">
    <div class="flex <?php echo $sizeConfig['inner']; ?> animate-spin items-center justify-center rounded-full border-4 border-transparent <?php echo $innerColorClass; ?>"></div>
  </div>
  <?php if ($text): ?>
    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($text); ?></p>
  <?php endif; ?>
</div>