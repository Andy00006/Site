(function () {
    // Appliquer le thème immédiatement pour éviter le flash
    var saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);

    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('dark-toggle');
        if (!btn) return;

        // Sync icônes au chargement
        updateIcons(document.documentElement.getAttribute('data-theme') === 'dark');

        btn.addEventListener('click', function () {
            var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            var next = isDark ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateIcons(!isDark);
        });
    });

    function updateIcons(isDark) {
        var btn = document.getElementById('dark-toggle');
        if (!btn) return;
        btn.setAttribute('aria-label', isDark ? 'Passer en mode clair' : 'Passer en mode sombre');
        btn.setAttribute('title',      isDark ? 'Mode clair' : 'Mode sombre');
    }
})();
