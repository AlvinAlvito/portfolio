(() => {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;

    const startedAt = performance.now();
    let hasFinished = false;

    const finishLoading = () => {
        if (hasFinished) return;
        hasFinished = true;

        const minimumDisplay = 650;
        const remaining = Math.max(0, minimumDisplay - (performance.now() - startedAt));
        window.setTimeout(() => {
            loader.classList.add('is-hidden');
            loader.setAttribute('aria-hidden', 'true');
            document.body.classList.add('page-is-ready');
        }, remaining);
    };

    if (document.readyState === 'complete') {
        finishLoading();
    } else {
        window.addEventListener('load', finishLoading, { once: true });
        window.setTimeout(finishLoading, 4000);
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;

        const destination = new URL(link.href, window.location.href);
        if (destination.origin !== window.location.origin) return;
        if (destination.pathname === window.location.pathname && destination.search === window.location.search && destination.hash) return;

        loader.classList.remove('is-hidden');
        loader.classList.add('is-leaving');
        loader.setAttribute('aria-hidden', 'false');
    });

    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;
        loader.classList.add('is-hidden');
        loader.classList.remove('is-leaving');
        loader.setAttribute('aria-hidden', 'true');
    });
})();
