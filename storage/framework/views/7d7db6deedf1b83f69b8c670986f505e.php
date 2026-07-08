<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        
        <meta name="description" content="Reservasi lapangan bulutangkis online dengan mudah dan cepat.">
        <meta name="keywords" content="reservasi, lapangan, bulutangkis, badminton, online">
        <meta name="author" content="Reservasi Bulutangkis">

        
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?php echo e(config('app.name', 'Reservasi Bulutangkis')); ?>">
        <meta property="og:description" content="Reservasi lapangan bulutangkis online dengan mudah dan cepat.">
        <meta property="og:locale" content="id_ID">

        
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="alternate icon" href="/favicon.ico">

        
        <title inertia><?php echo e(config('app.name', 'Reservasi Bulutangkis')); ?></title>

        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        
        <?php echo app('Tighten\Ziggy\BladeRouteGenerator')->generate(); ?>

        
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

        
        <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>
    </head>
    <body class="h-full font-sans antialiased bg-cream-bg text-cream-text">
        <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } else { ?><div id="app" data-page="<?php echo e(json_encode($page)); ?>"></div><?php } ?>
    </body>
</html>
<?php /**PATH D:\SEM 6\SOFTWERE TESTING\TUGAS FINAL\resources\views\app.blade.php ENDPATH**/ ?>