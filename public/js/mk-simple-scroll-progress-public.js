(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const scrollProgress = document.getElementById('mk-scroll-progress-indicator');

        if (!scrollProgress) {
            return;
        }

        const updateProgressIndicator = function() {
            const scrollTop = window.scrollY;
            const height = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / height) * 100;
            scrollProgress.style.width = `${scrollPercent}%`;
        };

        window.addEventListener('scroll', updateProgressIndicator);

        updateProgressIndicator();
    });
})();