<?php
/**
 * Simple Loader Component
 * 
 * Usage:
 * include 'components/ui/loader.php';
 * 
 * Optional parameters:
 * - $size: 'sm', 'md', 'lg' (default: 'md')
 * - $color: 'primary', 'blue', 'red', 'green' (default: 'primary')
 */

// Default values
$size = $size ?? 'md';
$color = $color ?? 'primary';

// Size classes
$sizes = [
    'sm' => 'h-6 w-6',
    'md' => 'h-10 w-10', 
    'lg' => 'h-16 w-16'
];

// Color classes
$colors = [
    'primary' => 'border-primary',
    'blue' => 'border-blue-500',
    'red' => 'border-red-500',
    'green' => 'border-green-500'
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$colorClass = $colors[$color] ?? $colors['primary'];
?>

<div class="flex <?php echo $sizeClass; ?> animate-spin items-center justify-center rounded-full border-4 border-t-transparent <?php echo $colorClass; ?>"></div>