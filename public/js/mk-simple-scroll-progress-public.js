(function() {
    'use strict';

    function init() {
        const scrollProgress = document.getElementById('mk-scroll-progress-indicator');

        if (!scrollProgress) {
            return;
        }

        let framePending = false;

        const updateProgressIndicator = function() {
            framePending = false;

            const root = document.documentElement;
            const scrollingElement = document.scrollingElement || root;
            const viewportHeight = root.clientHeight || window.innerHeight;
            const documentHeight = Math.max(
                scrollingElement.scrollHeight,
                root.scrollHeight,
                document.body ? document.body.scrollHeight : 0
            );
            const scrollableHeight = documentHeight - viewportHeight;
            const scrollTop = typeof window.scrollY === 'number' ? window.scrollY : scrollingElement.scrollTop;
            const scrollPercent = scrollableHeight > 0
                ? Math.min(100, Math.max(0, scrollTop / scrollableHeight * 100))
                : 0;
            const width = `${scrollPercent}%`;

            if (scrollProgress.style.width !== width) {
                scrollProgress.style.width = width;
            }
        };

        const scheduleUpdate = function() {
            if (!framePending) {
                framePending = true;
                window.requestAnimationFrame(updateProgressIndicator);
            }
        };

        window.addEventListener('scroll', scheduleUpdate, { passive: true });
        window.addEventListener('resize', scheduleUpdate);
        window.addEventListener('pageshow', scheduleUpdate);
        window.addEventListener('load', scheduleUpdate);
        document.addEventListener('load', scheduleUpdate, true);

        if (typeof window.ResizeObserver === 'function') {
            const resizeObserver = new window.ResizeObserver(scheduleUpdate);
            resizeObserver.observe(document.documentElement);

            if (document.body) {
                resizeObserver.observe(document.body);
            }
        } else if (typeof window.MutationObserver === 'function') {
            const mutationObserver = new window.MutationObserver(function(records) {
                if (records.some(function(record) { return record.target !== scrollProgress; })) {
                    scheduleUpdate();
                }
            });

            mutationObserver.observe(document.documentElement, {
                attributes: true,
                childList: true,
                characterData: true,
                subtree: true
            });
        }

        updateProgressIndicator();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
