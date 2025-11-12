<?php
require_once __DIR__ . '/../utils/ThemeHelper.php';

$title = $title ?? 'Welcome';
$subtitle = $subtitle ?? 'Continue your journey';
$illustration = $illustration ?? 'default.svg';
$theme = $theme ?? 'light';

$themeClasses = ThemeHelper::getThemeClasses($theme);
$bgClass = $themeClasses['bg'];
$textClass = $themeClasses['text'];
$borderClass = $themeClasses['border'];
$iconBgClass = $themeClasses['iconBg'];
$iconTextClass = $themeClasses['iconText'];
?>

<div class="hidden md:flex md:w-1/2 <?php echo $bgClass; ?> border-r <?php echo $borderClass; ?> overflow-hidden relative z-0">
    <div class="absolute right-0 bottom-0 md:translate-y-6 lg:translate-y-10 xl:translate-y-12 w-full md:w-[85%] lg:w-[80%] xl:w-3/4 md:min-w-[280px] lg:min-w-[320px] xl:min-w-[360px] pointer-events-none animate-fade-in" style="animation-delay: 150ms; z-index: 0;">
        <img
            src="assets/images/illustrations/<?php echo htmlspecialchars($illustration); ?>"
            alt="Illustration"
            class="w-full h-auto object-contain"
            onerror="this.style.display='none'"
        >
    </div>

    <div class="w-full h-full flex flex-col p-8 md:p-10 lg:p-12 relative z-10">
        <div class="animate-fade-in">
            <a href="index.php" class="text-xl md:text-2xl font-bold <?php echo $textClass; ?>">
                SkillLink
            </a>
        </div>

        <div class="mt-16 md:mt-20 lg:mt-24 max-w-lg">
            <div class="animate-fade-in" style="animation-delay: 100ms;">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-4 md:mb-5 lg:mb-6 <?php echo $textClass; ?> leading-none tracking-tighter">
                    <?php echo htmlspecialchars($title); ?>
                </h1>
                <p class="text-base md:text-lg lg:text-xl font-semibold <?php echo $textClass; ?> leading-relaxed">
                    <?php echo htmlspecialchars($subtitle); ?>
                </p>
            </div>
        </div>
    </div>
</div>

