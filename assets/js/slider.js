/**
 * XtremeSlider — Frontend JavaScript
 * Supports Default (full-bleed), Cool (track-based), and 3D (perspective) layouts.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var sliders = document.querySelectorAll('.xs-slider-wrap');
        sliders.forEach(function (el) {
            new XtremeSlider(el);
        });
    });

    function XtremeSlider(el) {
        this.el        = el;
        this.layout    = el.dataset.layout || 'cool';
        this.visible   = parseInt(el.dataset.visible, 10) || 3;
        this.autoplay  = el.dataset.autoplay === 'true';
        this.speed     = parseInt(el.dataset.speed, 10) || 4000;
        this.total     = parseInt(el.dataset.total, 10) || 0;

        this.track     = el.querySelector('.xs-slider-track');
        this.slides    = el.querySelectorAll('.xs-slide');
        this.prevBtn   = el.querySelector('.xs-nav-prev');
        this.nextBtn   = el.querySelector('.xs-nav-next');
        this.dots      = el.querySelectorAll('.xs-dot');

        this.current   = 0;
        this.timer     = null;

        if (this.total === 0) return;

        this.init();
    }

    XtremeSlider.prototype.init = function () {
        var self = this;

        if (this.layout === '3d') {
            this.init3D();
        } else if (this.layout === 'default') {
            this.initDefault();
        } else {
            this.initCool();
        }

        // Hide arrows when there is nothing to scroll
        this.updateArrowVisibility();

        // Arrow events
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', function () {
                self.prev();
            });
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', function () {
                self.next();
            });
        }

        // Dot events
        this.dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                var page = parseInt(this.dataset.page, 10);
                self.goTo(page);
            });
        });

        // Autoplay
        if (this.autoplay) {
            this.startAutoplay();

            this.el.addEventListener('mouseenter', function () {
                self.stopAutoplay();
            });
            this.el.addEventListener('mouseleave', function () {
                self.startAutoplay();
            });
        }

        // Touch support
        this.initTouch();

        // Responsive recalc
        window.addEventListener('resize', function () {
            if (self.layout === '3d') {
                self.update3D();
            } else if (self.layout === 'default') {
                self.updateDefault();
            } else {
                self.updateCool();
            }
            self.updateArrowVisibility();
        });
    };

    /* ======================================================================
       DEFAULT LAYOUT — infinite with cloned slides for seamless looping
       ====================================================================== */
    XtremeSlider.prototype.initDefault = function () {
        this.cloneSlides();
        this.updateDefault();
    };

    XtremeSlider.prototype.cloneSlides = function () {
        if (this._cloned) return;
        this._cloned = true;

        var vis = this.getResponsiveVisible();
        var slidesArr = Array.prototype.slice.call(this.slides);
        var count = slidesArr.length;

        // Clone last `vis` slides and prepend
        for (var i = count - vis; i < count; i++) {
            var idx = Math.max(0, i);
            var clone = slidesArr[idx].cloneNode(true);
            clone.classList.add('xs-clone');
            this.track.insertBefore(clone, this.track.firstChild);
        }

        // Clone first `vis` slides and append
        for (var j = 0; j < vis; j++) {
            var idx2 = Math.min(j, count - 1);
            var clone2 = slidesArr[idx2].cloneNode(true);
            clone2.classList.add('xs-clone');
            this.track.appendChild(clone2);
        }

        // Re-query all slides including clones
        this.allSlides = this.track.querySelectorAll('.xs-slide');
        this.cloneCount = vis;
    };

    XtremeSlider.prototype.updateDefault = function () {
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;
        var vis      = this.getResponsiveVisible();
        // Slide width: vis full slides + 2 half-slides (peek) = vis + 1
        var slideW   = vpWidth / (vis + 1);

        var slides = this.allSlides || this.slides;
        for (var i = 0; i < slides.length; i++) {
            slides[i].style.width = slideW + 'px';
        }

        this.moveDefault(false);
    };

    XtremeSlider.prototype.moveDefault = function (animate) {
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;
        var vis      = this.getResponsiveVisible();
        var slideW   = vpWidth / (vis + 1);
        var clones   = this.cloneCount || 0;

        // Offset: position at clone boundary + half-slide peek from the left
        var pos    = this.current + clones;
        var offset = -(pos * slideW) + (slideW * 0.5);

        if (animate === false) {
            this.track.style.transition = 'none';
        } else {
            this.track.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        }

        this.track.style.transform = 'translateX(' + offset + 'px)';

        // Mark center slides with xs-center class
        var allSlides = this.allSlides || this.slides;
        var centerStart = pos;       // first fully visible slide index in allSlides
        var centerEnd   = pos + vis; // exclusive
        for (var s = 0; s < allSlides.length; s++) {
            if (s >= centerStart && s < centerEnd) {
                allSlides[s].classList.add('xs-center');
            } else {
                allSlides[s].classList.remove('xs-center');
            }
        }

        // After transition, silently jump if we're on a clone
        if (animate !== false) {
            var self = this;
            var total = this.total;
            clearTimeout(this._jumpTimer);
            this._jumpTimer = setTimeout(function () {
                var jumped = false;
                if (self.current >= total) {
                    self.current = 0;
                    jumped = true;
                } else if (self.current < 0) {
                    self.current = total - 1;
                    jumped = true;
                }
                if (jumped) {
                    self.moveDefault(false);
                    // Force reflow then restore transition
                    self.track.offsetHeight;
                    self.track.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                }
            }, 520);
        }
    };

    /* ======================================================================
       SIMPLE LAYOUT
       ====================================================================== */
    XtremeSlider.prototype.initCool = function () {
        this.updateCool();
    };

    XtremeSlider.prototype.updateCool = function () {
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;
        var vis      = this.getResponsiveVisible();
        var slideW   = vpWidth / vis;

        this.slides.forEach(function (slide) {
            slide.style.width = slideW + 'px';
        });

        this.moveCool();
    };

    XtremeSlider.prototype.moveCool = function () {
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;
        var vis      = this.getResponsiveVisible();
        var slideW   = vpWidth / vis;
        var offset   = -(this.current * slideW);

        this.track.style.transform = 'translateX(' + offset + 'px)';
        this.updateDots();
    };

    XtremeSlider.prototype.getResponsiveVisible = function () {
        var w = window.innerWidth;
        if (w <= 480) return Math.min(this.visible, 1);
        if (w <= 768) return Math.min(this.visible, 2);
        if (w <= 1024) return Math.min(this.visible, 3);
        return this.visible;
    };

    XtremeSlider.prototype.getMaxPage = function () {
        if (this.layout === '3d') {
            return this.total - 1;
        }
        var vis = this.getResponsiveVisible();
        return Math.max(0, this.total - vis);
    };

    /* ======================================================================
       3D LAYOUT
       ====================================================================== */
    XtremeSlider.prototype.init3D = function () {
        this.update3D();
    };

    XtremeSlider.prototype.update3D = function () {
        var self     = this;
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;

        // Calculate slide width based on viewport — center slide is larger
        var slideW = Math.min(vpWidth * 0.55, 600);
        var slideH = viewport.offsetHeight;

        this.slides.forEach(function (slide, i) {
            var diff = i - self.current;

            slide.style.width = slideW + 'px';
            slide.classList.remove('xs-active');

            if (diff === 0) {
                // Center (active) slide
                slide.style.transform = 'translateX(-50%) translateZ(0) scale(1)';
                slide.style.left = '50%';
                slide.style.opacity = '1';
                slide.style.zIndex = '5';
                slide.classList.add('xs-active');
            } else if (Math.abs(diff) === 1) {
                // Adjacent slides
                var xOff = diff > 0 ? '20%' : '-120%';
                slide.style.transform = 'translateX(0) translateZ(-150px) rotateY(' + (diff * -25) + 'deg) scale(0.85)';
                slide.style.left = diff > 0 ? '62%' : '-17%';
                slide.style.opacity = '0.7';
                slide.style.zIndex = '3';
            } else if (Math.abs(diff) === 2) {
                // Far slides
                slide.style.transform = 'translateX(0) translateZ(-300px) rotateY(' + (diff * -30) + 'deg) scale(0.7)';
                slide.style.left = diff > 0 ? '80%' : '-35%';
                slide.style.opacity = '0.4';
                slide.style.zIndex = '1';
            } else {
                // Hidden slides
                slide.style.transform = 'translateZ(-500px) scale(0.5)';
                slide.style.left = diff > 0 ? '100%' : '-55%';
                slide.style.opacity = '0';
                slide.style.zIndex = '0';
            }
        });

        this.updateDots();
    };

    /* ======================================================================
       ARROW VISIBILITY
       Hide prev/next arrows when all slides fit in the viewport (nothing to scroll).
       Re-evaluated on init and every resize so responsive breakpoints are respected.
       ====================================================================== */
    XtremeSlider.prototype.updateArrowVisibility = function () {
        var hidden = this.getMaxPage() === 0;
        if (this.prevBtn) { this.prevBtn.style.display = hidden ? 'none' : ''; }
        if (this.nextBtn) { this.nextBtn.style.display = hidden ? 'none' : ''; }
    };

    /* ======================================================================
       NAVIGATION
       ====================================================================== */
    XtremeSlider.prototype.next = function () {
        if (this.layout === 'default') {
            // Infinite: go beyond bounds, clone jump handles reset
            this.current = this.current + 1;
        } else {
            var max = this.getMaxPage();
            this.current = this.current >= max ? 0 : this.current + 1;
        }
        this.update();
    };

    XtremeSlider.prototype.prev = function () {
        if (this.layout === 'default') {
            this.current = this.current - 1;
        } else {
            var max = this.getMaxPage();
            this.current = this.current <= 0 ? max : this.current - 1;
        }
        this.update();
    };

    XtremeSlider.prototype.goTo = function (page) {
        this.current = Math.max(0, Math.min(page, this.getMaxPage()));
        this.update();
    };

    XtremeSlider.prototype.update = function () {
        if (this.layout === '3d') {
            this.update3D();
        } else if (this.layout === 'default') {
            this.moveDefault();
        } else {
            this.moveCool();
        }
    };

    XtremeSlider.prototype.updateDots = function () {
        var self = this;
        var activePage;

        if (this.layout === '3d') {
            activePage = this.current;
        } else {
            var vis = this.getResponsiveVisible();
            activePage = Math.floor(this.current / vis);
        }

        this.dots.forEach(function (dot, i) {
            dot.classList.toggle('active', i === activePage);
        });
    };

    /* ======================================================================
       AUTOPLAY
       ====================================================================== */
    XtremeSlider.prototype.startAutoplay = function () {
        var self = this;
        this.stopAutoplay();
        this.timer = setInterval(function () {
            self.next();
        }, this.speed);
    };

    XtremeSlider.prototype.stopAutoplay = function () {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    };

    /* ======================================================================
       TOUCH / SWIPE
       ====================================================================== */
    XtremeSlider.prototype.initTouch = function () {
        var self       = this;
        var startX     = 0;
        var startY     = 0;
        var isDragging = false;

        // ── Touch ──────────────────────────────────────────────────────────
        this.el.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isDragging = true;
        }, { passive: true });

        this.el.addEventListener('touchmove', function (e) {
            // Allow vertical scroll if mostly vertical swipe
        }, { passive: true });

        this.el.addEventListener('touchend', function (e) {
            if (!isDragging) return;
            isDragging = false;

            var diffX = e.changedTouches[0].clientX - startX;
            var diffY = e.changedTouches[0].clientY - startY;

            if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) { self.next(); } else { self.prev(); }
            }
        }, { passive: true });

        // ── Mouse drag (default layout only) ───────────────────────────────
        if (this.layout !== 'default') return;

        var mouseDown  = false;
        var mouseMoved = false;
        var track      = this.track;

        this.el.addEventListener('mousedown', function (e) {
            // Prevent text selection and native image drag during mouse-drag
            e.preventDefault();
            startX     = e.clientX;
            mouseDown  = true;
            mouseMoved = false;
            track.style.transition = 'none';
        });

        window.addEventListener('mousemove', function (e) {
            if (!mouseDown) return;
            if (Math.abs(e.clientX - startX) > 5) {
                mouseMoved = true;
                self.el.style.cursor = 'grabbing';
            }
        });

        window.addEventListener('mouseup', function (e) {
            if (!mouseDown) return;
            mouseDown = false;
            self.el.style.cursor = '';
            track.style.transition = '';

            if (!mouseMoved) return;

            var diffX = e.clientX - startX;
            if (Math.abs(diffX) > 40) {
                if (diffX < 0) { self.next(); } else { self.prev(); }
            } else {
                // Snap back without navigating
                self.moveDefault(false);
            }
        });

        // Prevent click-through on links after a drag
        this.el.addEventListener('click', function (e) {
            if (mouseMoved) {
                e.preventDefault();
                e.stopPropagation();
                mouseMoved = false;
            }
        }, true);
    };

})();
