/**
 * VR Doctors theme interactions (nav, carousels, tabs, counters, lightbox).
 */
(function () {
  "use strict";

  function qs(sel, root) {
    return (root || document).querySelector(sel);
  }
  function qsa(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  /* ——— Nav ——— */
  function initNav() {
    var toggle = qs("[data-mobile-toggle]");
    var menu = qs("[data-mobile-menu]");

    if (toggle && menu) {
      toggle.addEventListener("click", function () {
        menu.classList.toggle("hidden");
      });
    }
    // Menu links are managed in the admin, so the row can outgrow the bar at
    // any width. When it does, fall back to the burger menu.
    var nav = qs("[data-vr-nav]");
    var desk = qs("[data-desktop-nav]");
    if (nav && desk) {
      var bar = desk.parentElement;
      var fit = function () {
        nav.classList.remove("vr-nav--compact");
        if (getComputedStyle(desk).display === "none") return;
        var used = 0;
        Array.prototype.forEach.call(bar.children, function (el) {
          if (getComputedStyle(el).display !== "none") used += el.getBoundingClientRect().width;
        });
        var cs = getComputedStyle(bar);
        var room = bar.clientWidth - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
        if (used > room - 16) nav.classList.add("vr-nav--compact");
      };
      var queued = false;
      window.addEventListener("resize", function () {
        if (queued) return;
        queued = true;
        requestAnimationFrame(function () { queued = false; fit(); });
      });
      fit();
      // Web fonts change link widths once they load.
      if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
    }

    // Any menu item can have a dropdown (set in Admin → Menus), so bind every
    // pair. On mobile each toggle opens the panel that directly follows it.
    qsa("[data-mobile-courses-toggle]").forEach(function (btn) {
      var panel = btn.nextElementSibling;
      if (!panel || !panel.hasAttribute("data-mobile-courses")) return;
      btn.addEventListener("click", function () {
        panel.classList.toggle("hidden");
      });
    });
    qsa("[data-courses-dropdown]").forEach(function (drop) {
      var dropMenu = qs("[data-courses-menu]", drop);
      if (!dropMenu) return;
      var show = function () {
        dropMenu.classList.remove("hidden");
        // A dropdown on one of the last links would run off the screen.
        dropMenu.style.left = "";
        dropMenu.style.right = "";
        if (dropMenu.getBoundingClientRect().right > window.innerWidth - 8) {
          dropMenu.style.left = "auto";
          dropMenu.style.right = "0";
        }
      };
      drop.addEventListener("mouseenter", show);
      drop.addEventListener("mouseleave", function () {
        dropMenu.classList.add("hidden");
      });
      // Keyboard users reach the links by tabbing into the dropdown.
      drop.addEventListener("focusin", show);
      drop.addEventListener("focusout", function (e) {
        if (!drop.contains(e.relatedTarget)) dropMenu.classList.add("hidden");
      });
    });
  }

  /* ——— YouTube facades ——— */
  // Each embedded player costs ~500 KB of script and dozens of requests, so
  // show the thumbnail and only load YouTube when the visitor asks for it.
  function initYouTubeFacades() {
    qsa("[data-yt-facade]").forEach(function (frame) {
      var btn = qs("button", frame);
      if (!btn) return;
      btn.addEventListener("click", function () {
        var id = frame.getAttribute("data-yt-id");
        if (!id || frame.classList.contains("is-playing")) return;
        frame.classList.add("is-playing");
        var iframe = document.createElement("iframe");
        iframe.src = "https://www.youtube.com/embed/" + encodeURIComponent(id) + "?autoplay=1&rel=0&modestbranding=1";
        iframe.title = btn.getAttribute("aria-label") || "YouTube video";
        iframe.className = "absolute inset-0 w-full h-full";
        iframe.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share");
        iframe.setAttribute("allowfullscreen", "");
        frame.innerHTML = "";
        frame.appendChild(iframe);
      });
    });
  }

  /* ——— Hero carousel ——— */
  function initHero() {
    var root = qs("[data-hero-carousel]");
    if (!root) return;
    var slides = qsa("[data-hero-slide]", root);
    if (!slides.length) return;
    var dots = qsa("[data-hero-dot]", root);
    var current = 0;
    var timer;

    function show(i) {
      current = (i + slides.length) % slides.length;
      slides.forEach(function (s, idx) {
        // Keep slides at z-0 so the dark overlay (z-[1]) stays above them
        s.classList.toggle("opacity-100", idx === current);
        s.classList.toggle("opacity-0", idx !== current);
        s.setAttribute("aria-hidden", idx === current ? "false" : "true");
      });
      dots.forEach(function (d, idx) {
        d.classList.toggle("bg-orange-500", idx === current);
        d.classList.toggle("bg-white/50", idx !== current);
      });
      var content = qs("[data-hero-content]", root);
      if (content) {
        var s = slides[current];
        var sub = content.querySelector("[data-hero-subtitle]");
        var title = content.querySelector("[data-hero-title]");
        var desc = content.querySelector("[data-hero-desc]");
        if (sub) sub.textContent = s.getAttribute("data-subtitle") || "";
        if (title) title.textContent = s.getAttribute("data-title") || "";
        if (desc) desc.textContent = s.getAttribute("data-description") || "";
      }
    }

    function next() {
      show(current + 1);
    }
    function prev() {
      show(current - 1);
    }
    function start() {
      clearInterval(timer);
      timer = setInterval(next, 6000);
    }

    var nextBtn = qs("[data-hero-next]", root);
    var prevBtn = qs("[data-hero-prev]", root);
    if (nextBtn) nextBtn.addEventListener("click", function () { next(); start(); });
    if (prevBtn) prevBtn.addEventListener("click", function () { prev(); start(); });
    dots.forEach(function (d, i) {
      d.addEventListener("click", function () { show(i); start(); });
    });
    show(0);
    start();
  }

  /* ——— Stats counters ——— */
  function initCounters() {
    qsa("[data-counter]").forEach(function (el) {
      var target = parseInt(el.getAttribute("data-target"), 10) || 0;
      var suffix = el.getAttribute("data-suffix") || "";
      var numEl = el.querySelector("[data-counter-num]");
      if (!numEl) return;
      var raf = null;

      function animate() {
        var start = null;
        var duration = 1800;
        function step(now) {
          if (!start) start = now;
          var progress = Math.min((now - start) / duration, 1);
          var ease = 1 - Math.pow(1 - progress, 3);
          var val = Math.round(target * ease);
          numEl.textContent = val + (val === target ? suffix : "");
          if (progress < 1) raf = requestAnimationFrame(step);
        }
        raf = requestAnimationFrame(step);
      }

      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (raf) cancelAnimationFrame(raf);
            if (entry.isIntersecting) animate();
            else numEl.textContent = "0";
          });
        },
        { threshold: 0.3 }
      );
      obs.observe(el);
    });
  }

  /* ——— Testimonials ——— */
  function initTestimonials() {
    var root = qs("[data-testimonials]");
    if (!root) return;
    var cards = qsa("[data-testimonial-card]", root);
    if (!cards.length) return;
    var current = 0;
    var dots = qsa("[data-testimonial-dot]", root);

    function render() {
      var a = current;
      var b = (current + 1) % cards.length;
      cards.forEach(function (c, i) {
        var showDesktop = i === a || i === b;
        var showMobile = i === a;
        c.classList.toggle("md:block", showDesktop);
        c.classList.toggle("md:hidden", !showDesktop);
        c.classList.toggle("hidden", !showMobile);
        c.classList.toggle("block", showMobile);
        if (showDesktop && !showMobile) {
          c.classList.remove("hidden");
          c.classList.add("hidden", "md:block");
        }
      });
      // Simpler: hide all, show two for desktop via classes
      cards.forEach(function (c, i) {
        c.style.display = "none";
        c.classList.remove("is-active");
      });
      cards[a].style.display = "";
      cards[a].classList.add("is-active");
      cards[a].setAttribute("data-slot", "primary");
      if (cards[b]) {
        cards[b].style.display = "";
        cards[b].setAttribute("data-slot", "secondary");
      }
      // Use parent grid: only inject visible into desktop/mobile containers
      var desktop = qs("[data-testimonials-desktop]", root);
      var mobile = qs("[data-testimonials-mobile]", root);
      if (desktop) {
        desktop.innerHTML = "";
        desktop.appendChild(cards[a].cloneNode(true));
        desktop.appendChild(cards[b].cloneNode(true));
        desktop.querySelectorAll("[data-testimonial-card]").forEach(function (n) {
          n.style.display = "";
          n.classList.remove("hidden");
        });
      }
      if (mobile) {
        mobile.innerHTML = "";
        mobile.appendChild(cards[a].cloneNode(true));
        mobile.querySelectorAll("[data-testimonial-card]")[0].style.display = "";
      }
      dots.forEach(function (d, i) {
        d.classList.toggle("bg-orange-500", i === current);
        d.classList.toggle("scale-125", i === current);
        d.classList.toggle("bg-gray-300", i !== current);
      });
    }

    var source = qs("[data-testimonials-source]", root);
    if (source) {
      // cards already in source
    }

    var nextBtn = qs("[data-testimonial-next]", root);
    var prevBtn = qs("[data-testimonial-prev]", root);
    if (nextBtn) nextBtn.addEventListener("click", function () {
      current = (current + 1) % cards.length;
      render();
    });
    if (prevBtn) prevBtn.addEventListener("click", function () {
      current = current === 0 ? cards.length - 1 : current - 1;
      render();
    });
    dots.forEach(function (d, i) {
      d.addEventListener("click", function () {
        current = i;
        render();
      });
    });
    setInterval(function () {
      current = (current + 1) % cards.length;
      render();
    }, 5000);
    render();
  }

  /* ——— Generic tabs (campus, approach, timeline, journey steps) ——— */
  function initTabs() {
    qsa("[data-tabs]").forEach(function (root) {
      var buttons = qsa("[data-tab-btn]", root);
      var panels = qsa("[data-tab-panel]", root);
      if (!buttons.length || !panels.length) return;

      function activate(index) {
        buttons.forEach(function (btn, i) {
          var on = i === index;
          btn.classList.toggle("is-active", on);
          btn.setAttribute("aria-selected", on ? "true" : "false");
          // Apply active classes from data attributes if present
          var activeCls = (btn.getAttribute("data-active-class") || "").split(/\s+/).filter(Boolean);
          var inactiveCls = (btn.getAttribute("data-inactive-class") || "").split(/\s+/).filter(Boolean);
          inactiveCls.forEach(function (c) { btn.classList.remove(c); });
          activeCls.forEach(function (c) { btn.classList.remove(c); });
          (on ? activeCls : inactiveCls).forEach(function (c) { btn.classList.add(c); });
        });
        panels.forEach(function (panel, i) {
          var on = i === index;
          panel.classList.toggle("hidden", !on);
          panel.setAttribute("aria-hidden", on ? "false" : "true");
        });
      }

      buttons.forEach(function (btn, i) {
        btn.addEventListener("click", function () {
          activate(i);
        });
      });
      activate(0);
    });
  }

  /* ——— BiPC ecosystem (matches Next HealthcareEcosystem) ——— */
  function initBipc() {
    var root = qs("[data-bipc-ecosystem]");
    if (!root) return;
    var fields = qsa("[data-bipc-field]", root);
    var careerLists = qsa("[data-bipc-careers]", root);
    var details = qsa("[data-bipc-detail]", root);
    if (!fields.length || !careerLists.length) return;
    var eco = 0;
    var car = 0;

    function setFieldActive(el, on) {
      el.classList.toggle("bg-blue-900", on);
      el.classList.toggle("text-white", on);
      el.classList.toggle("bg-blue-50", !on);
      el.classList.toggle("text-blue-900", !on);
      el.classList.toggle("hover:bg-blue-100", !on);
    }

    function setCareerActive(el, on) {
      el.classList.toggle("bg-orange-500", on);
      el.classList.toggle("text-white", on);
      el.classList.toggle("bg-blue-50", !on);
      el.classList.toggle("text-blue-900", !on);
      el.classList.toggle("hover:bg-blue-100", !on);
    }

    function render() {
      fields.forEach(function (f, i) {
        setFieldActive(f, i === eco);
      });
      careerLists.forEach(function (list, i) {
        list.classList.toggle("hidden", i !== eco);
      });
      var activeList = careerLists[eco];
      if (activeList) {
        var careers = qsa("[data-bipc-career]", activeList);
        // Clamp career index if field has fewer options.
        if (car >= careers.length) car = 0;
        careers.forEach(function (c, i) {
          setCareerActive(c, i === car);
        });
      }
      details.forEach(function (d) {
        var match =
          parseInt(d.getAttribute("data-eco"), 10) === eco &&
          parseInt(d.getAttribute("data-car"), 10) === car;
        d.classList.toggle("hidden", !match);
      });
    }

    fields.forEach(function (f, i) {
      f.addEventListener("click", function () {
        eco = i;
        car = 0;
        render();
        if (window.innerWidth < 768) {
          var target = qs("[data-bipc-careers-panel]", root);
          if (target) {
            setTimeout(function () {
              target.scrollIntoView({ behavior: "smooth", block: "start" });
            }, 150);
          }
        }
      });
    });

    careerLists.forEach(function (list) {
      qsa("[data-bipc-career]", list).forEach(function (c, i) {
        c.addEventListener("click", function () {
          car = i;
          render();
          if (window.innerWidth < 768) {
            var target = qs("[data-bipc-detail-panel]", root);
            if (target) {
              setTimeout(function () {
                target.scrollIntoView({ behavior: "smooth", block: "start" });
              }, 150);
            }
          }
        });
      });
    });

    render();
  }

  /* ——— Lightbox ——— */
  function initLightbox() {
    var root = qs("[data-lightbox-root]");
    if (!root) return;
    var overlay = qs("[data-lightbox-overlay]", root);
    var img = qs("[data-lightbox-img]", root);
    var closeBtn = qs("[data-lightbox-close]", root);

    function open(src) {
      if (!overlay || !img) return;
      img.src = src;
      overlay.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }
    function close() {
      if (!overlay) return;
      overlay.classList.add("hidden");
      document.body.style.overflow = "";
    }

    qsa("[data-lightbox-src]", root).forEach(function (el) {
      el.addEventListener("click", function () {
        open(el.getAttribute("data-lightbox-src") || el.src);
      });
    });
    if (closeBtn) closeBtn.addEventListener("click", close);
    if (overlay) {
      overlay.addEventListener("click", function (e) {
        if (e.target === overlay) close();
      });
    }
  }

  /* ——— FAQ accordion ——— */
  function initFaq() {
    qsa("[data-faq-accordion]").forEach(function (root) {
      qsa("[data-faq-item]", root).forEach(function (item) {
        var toggle = qs("[data-faq-toggle]", item);
        var panel = qs("[data-faq-panel]", item);
        var icon = qs("[data-faq-icon]", item);
        if (!toggle || !panel) return;

        toggle.addEventListener("click", function () {
          var isOpen = toggle.getAttribute("aria-expanded") === "true";

          // Close others in this accordion
          qsa("[data-faq-item]", root).forEach(function (other) {
            var otherToggle = qs("[data-faq-toggle]", other);
            var otherPanel = qs("[data-faq-panel]", other);
            var otherIcon = qs("[data-faq-icon]", other);
            if (!otherToggle || !otherPanel) return;
            otherToggle.setAttribute("aria-expanded", "false");
            otherPanel.classList.add("hidden");
            if (otherIcon) otherIcon.classList.remove("rotate-180");
          });

          if (!isOpen) {
            toggle.setAttribute("aria-expanded", "true");
            panel.classList.remove("hidden");
            if (icon) icon.classList.add("rotate-180");
          }
        });
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initNav();
    initYouTubeFacades();
    initHero();
    initCounters();
    initTestimonials();
    initTabs();
    initBipc();
    initLightbox();
    initFaq();
  });
})();
