(function () {
    var saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);

    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('dark-toggle');
        if (!btn) return;
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
       if (isDark) {
    btn.setAttribute('aria-label', 'Passer en mode clair');
    btn.setAttribute('title', 'Mode clair');
} else {
    btn.setAttribute('aria-label', 'Passer en mode sombre');
    btn.setAttribute('title', 'Mode sombre');
}
    }
})();
