<header class="sticky top-0 z-50 bg-gradient-to-r from-black via-red-950 to-black backdrop-blur-lg border-b border-yellow-500/20 w-full py-2">
    <div class="flex flex-row justify-between items-center w-[80%] max-w-7xl mx-auto
                sm:w-full
                md:w-[80%] md:flex-row
                flex-col gap-2 md:gap-0">
        <div class="flex items-center gap-2 w-full md:w-auto justify-center md:justify-start">
            <img src="./assets/siglat.png" alt="Siglat Logo" class="w-10 h-10 rounded-lg border border-[#5e81ac] shadow bg-[#3b4252] p-1">
            <h1 class="text-2xl font-bold tracking-wide text-white drop-shadow px-2">Siglat</h1>
        </div>

        <div class="flex items-center gap-3 text-white w-full md:w-auto justify-center md:justify-end mt-2 md:mt-0">
            <?php include "./pages/client/feats/weather.php"; ?>
            <?php include "./pages/client/feats/sidebar.php"; ?>
        </div>
    </div>
</header>
