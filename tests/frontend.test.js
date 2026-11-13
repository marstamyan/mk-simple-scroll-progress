const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../public/js/mk-simple-scroll-progress-public.js'), 'utf8');

function setup(options = {}) {
    const listeners = { window: {}, document: {} };
    const frames = [];
    const observers = [];
    const mutations = [];
    const indicator = { style: {} };
    const root = { clientHeight: 800, scrollHeight: 2400, scrollTop: 0 };
    const body = { scrollHeight: 2400 };
    const document = {
        documentElement: root,
        body,
        scrollingElement: root,
        readyState: options.readyState || 'complete',
        getElementById: () => options.missing ? null : indicator,
        addEventListener: (event, callback, config) => { listeners.document[event] = { callback, config }; }
    };
    const window = {
        scrollY: options.scrollY === undefined ? 0 : options.scrollY,
        innerHeight: 800,
        addEventListener: (event, callback, config) => { listeners.window[event] = { callback, config }; },
        requestAnimationFrame: callback => { frames.push(callback); }
    };

    if (options.resizeObserver !== false) {
        window.ResizeObserver = class {
            constructor(callback) { this.callback = callback; this.targets = []; observers.push(this); }
            observe(target) { this.targets.push(target); }
        };
    }
    window.MutationObserver = class {
        constructor(callback) { this.callback = callback; mutations.push(this); }
        observe() {}
    };

    if (options.configure) {
        options.configure({ document, window, root, body });
    }
    vm.runInNewContext(source, { document, window });

    return {
        document, window, root, body, indicator, listeners, frames, observers, mutations,
        event: (target, event) => listeners[target][event].callback(),
        flush: () => { while (frames.length) { frames.shift()(); } }
    };
}

test('initializes using a restored scroll position, including when loaded after DOMContentLoaded', () => {
    const page = setup({ scrollY: 800 });
    assert.equal(page.indicator.style.width, '50%');
    assert.equal(page.listeners.document.DOMContentLoaded, undefined);
});

test('waits for the DOM when loaded early and exits safely on excluded pages', () => {
    const page = setup({ readyState: 'loading' });
    assert.equal(page.indicator.style.width, undefined);
    assert.equal(page.listeners.document.DOMContentLoaded.config.once, true);
    page.event('document', 'DOMContentLoaded');
    assert.equal(page.indicator.style.width, '0%');
    const excluded = setup({ missing: true });
    assert.deepEqual(excluded.listeners.window, {});
    assert.equal(excluded.observers.length, 0);
});

test('short and viewport-sized pages never produce NaN or Infinity', () => {
    for (const height of [400, 800]) {
        const page = setup({ configure: ({ root, body }) => {
            root.scrollHeight = height;
            body.scrollHeight = height;
        } });
        assert.equal(page.indicator.style.width, '0%');
    }
});

test('clamps negative and overshooting scroll positions', () => {
    const page = setup({ scrollY: -40 });
    assert.equal(page.indicator.style.width, '0%');
    page.window.scrollY = 2000;
    page.event('window', 'scroll');
    page.flush();
    assert.equal(page.indicator.style.width, '100%');
});

test('batches repeated scrolls in one frame and reads the latest position', () => {
    const page = setup();
    assert.equal(page.listeners.window.scroll.config.passive, true);
    page.window.scrollY = 200;
    page.event('window', 'scroll');
    page.window.scrollY = 800;
    page.event('window', 'scroll');
    page.event('window', 'resize');
    assert.equal(page.frames.length, 1);
    assert.equal(page.indicator.style.width, '0%');
    page.flush();
    assert.equal(page.indicator.style.width, '50%');
    page.event('window', 'scroll');
    assert.equal(page.frames.length, 1);
});

test('uses the layout viewport and recomputes heights after resizing', () => {
    const page = setup({ scrollY: 800, configure: ({ window }) => { window.innerHeight = 600; } });
    assert.equal(page.indicator.style.width, '50%');
    page.root.clientHeight = 400;
    page.event('window', 'resize');
    page.flush();
    assert.equal(page.indicator.style.width, '40%');
});

test('tracks content height changes even when the user does not scroll', () => {
    const page = setup({ scrollY: 800 });
    assert.equal(page.observers[0].targets.length, 2);
    page.root.scrollHeight = 4000;
    page.body.scrollHeight = 4000;
    page.observers[0].callback();
    page.flush();
    assert.equal(page.indicator.style.width, '25%');
});

test('updates on page restore and on late resource loads', () => {
    const page = setup();
    page.window.scrollY = 1600;
    page.event('window', 'pageshow');
    page.flush();
    assert.equal(page.indicator.style.width, '100%');
    page.body.scrollHeight = 4000;
    page.event('document', 'load');
    page.flush();
    assert.equal(page.indicator.style.width, '50%');
    assert.equal(page.listeners.document.load.config, true);
    assert.ok(page.listeners.window.load);
});

test('falls back to scrollingElement and viewport metrics when needed', () => {
    const page = setup({ configure: ({ document, window, root }) => {
        delete window.scrollY;
        root.clientHeight = 0;
        document.scrollingElement = { scrollTop: 800, scrollHeight: 4000 };
    } });
    assert.equal(page.indicator.style.width, '25%');
});

test('older browsers can observe dynamic content without observing their own indicator writes', () => {
    const page = setup({ resizeObserver: false, scrollY: 800 });
    page.mutations[0].callback([{ target: page.indicator }]);
    assert.equal(page.frames.length, 0);
    page.body.scrollHeight = 4000;
    page.mutations[0].callback([{ target: page.body }]);
    page.flush();
    assert.equal(page.indicator.style.width, '25%');
});
