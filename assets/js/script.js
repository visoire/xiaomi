/* ============================================================
   Hardware Guide - script.js
   Non-commercial educational guide for Xiaomi/Brightway/LEQI flashing
   ============================================================ */

(function () {
  'use strict';

  // ---- Scroll animations ----------------------------------------
  function initFadeIn() {
    var els = document.querySelectorAll('.fade-in');
    if (!els.length) return;

    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });

    els.forEach(function (el) { obs.observe(el); });
  }

  // ---- Header scroll effect -------------------------------------
  function initHeader() {
    var header = document.querySelector('.header');
    if (!header) return;

    window.addEventListener('scroll', function () {
      header.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  // ---- Interactive Model Inspector -------------------------------
  function initProductSelector() {
    var select         = document.getElementById('scooterSelect');
    var connBadge      = document.getElementById('connectorBadge');
    var connText       = document.getElementById('connectorText');
    var flashableBadge = document.getElementById('flashableBadge');
    var flashableIcon  = document.getElementById('flashableIcon');
    var flashableText  = document.getElementById('flashableText');
    var mainImg        = document.getElementById('productMainImg');
    var detailsBox     = document.getElementById('inspectorDetails');
    var detailsConn    = document.getElementById('detailsConn');
    var detailsVolt    = document.getElementById('detailsVolt');
    var detailsCrossover = document.getElementById('detailsCrossover');
    var i18n           = window.i18n || {};

    if (!select) return;

    function setProductImage(targetSrc, targetAlt) {
      if (!mainImg) return;
      if (mainImg.getAttribute('src') === targetSrc) return;
      mainImg.style.opacity = '0.3';
      var imgLoader = new Image();
      imgLoader.onload = function () {
        mainImg.src = targetSrc;
        mainImg.alt = targetAlt;
        mainImg.style.opacity = '1';
      };
      imgLoader.onerror = function () {
        mainImg.src = targetSrc;
        mainImg.alt = targetAlt;
        mainImg.style.opacity = '1';
      };
      imgLoader.src = targetSrc;
    }

    function updateState() {
      var model = select.value.trim();

      if (!model) {
        if (connBadge)  connBadge.style.display = 'none';
        if (detailsBox) detailsBox.style.display = 'none';
        setProductImage('../assets/img/main.jpg', 'Flashing Cable Assembly');
        return;
      }

      var opt = select.querySelector('option[value="' + CSS.escape(model) + '"]');
      var conn = opt ? opt.getAttribute('data-connector') : '';
      var flashable = opt ? opt.getAttribute('data-flashable') === '1' : true;
      var isTesting = opt ? opt.getAttribute('data-testing') === '1' : false;

      // Update image based on connector type
      if (conn.indexOf('FEMALE') !== -1) {
        setProductImage('../assets/img/image_2.webp', model + ' - Julet M7 FEMALE Adapter');
      } else {
        setProductImage('../assets/img/image_1.webp', model + ' - Julet ' + (conn || 'MALE') + ' Adapter');
      }

      // Connector badge
      if (connBadge && connText && conn) {
        connText.textContent = (i18n.conn_label || 'Connector') + ': ' + conn;
        connBadge.style.display = 'flex';

        if (conn.indexOf('M8') !== -1) {
          connBadge.style.color = '#fb923c';
          connBadge.style.background = 'rgba(251,146,60,0.07)';
          connBadge.style.borderColor = 'rgba(251,146,60,0.2)';
        } else if (conn.indexOf('FEMALE') !== -1) {
          connBadge.style.color = '#60a5fa';
          connBadge.style.background = 'rgba(96,165,250,0.07)';
          connBadge.style.borderColor = 'rgba(96,165,250,0.2)';
        } else {
          connBadge.style.color = 'var(--accent)';
          connBadge.style.background = 'rgba(56, 189, 248,0.07)';
          connBadge.style.borderColor = 'rgba(56, 189, 248,0.2)';
        }
      }

      // Flashable badge
      if (flashableBadge && flashableIcon && flashableText) {
        if (isTesting) {
          flashableIcon.innerHTML = '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>';
          flashableIcon.setAttribute('stroke', '#fbbf24');
          flashableIcon.style.color = '#fbbf24';
          flashableText.textContent = (i18n.compat_flashable || 'Flashable') + ': ' + (i18n.flashable_testing || 'In Testing');
          flashableText.style.color = '#fbbf24';
        } else if (flashable) {
          flashableIcon.innerHTML = '<circle cx="12" cy="12" r="10"/><polyline points="20 6 9 17 4 12"/>';
          flashableIcon.setAttribute('stroke', 'var(--accent)');
          flashableIcon.style.color = 'var(--accent)';
          flashableText.textContent = (i18n.compat_flashable || 'Flashable') + ': ' + (i18n.flashable_yes || 'Supported');
          flashableText.style.color = 'var(--accent)';
        } else {
          flashableIcon.innerHTML = '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>';
          flashableIcon.setAttribute('stroke', 'var(--error)');
          flashableIcon.style.color = 'var(--error)';
          flashableText.textContent = (i18n.compat_flashable || 'Flashable') + ': ' + (i18n.flashable_no || 'Not Flashable');
          flashableText.style.color = 'var(--error)';
        }
      }

      // Technical details box
      if (detailsBox) {
        detailsBox.style.display = 'block';
        if (detailsConn) {
          detailsConn.textContent = conn + ' (5-Pin Julet)';
        }
        if (detailsVolt) {
          detailsVolt.textContent = '3.3V Logic Level (Do not supply external 5V VCC)';
        }
        if (detailsCrossover) {
          detailsCrossover.textContent = 'Adapter TX -> Scooter RX | Adapter RX -> Scooter TX | GND -> GND';
        }
      }
    }

    select.addEventListener('change', updateState);
    updateState();
  }

  // ---- Description tabs ----------------------------------------
  function initDescTabs() {
    var navItems = document.querySelectorAll('.description-nav-item');
    var tabs     = document.querySelectorAll('.desc-tab');
    if (!navItems.length) return;

    navItems.forEach(function (item) {
      item.addEventListener('click', function () {
        var target = item.getAttribute('data-tab');

        navItems.forEach(function (n) { n.classList.remove('active'); });
        tabs.forEach(function (t) { t.classList.remove('active'); });

        item.classList.add('active');
        var tab = document.getElementById('tab-' + target);
        if (tab) tab.classList.add('active');
      });
    });
  }

  // ---- Mobile Navigation ----------------------------------------
  function initMobileNav() {
    var burger = document.getElementById('burger');
    var mobNav = document.getElementById('mobNav');
    var overlay = document.getElementById('navOverlay');
    if (!burger || !mobNav || !overlay) return;

    function openMob() {
      burger.classList.add('open');
      mobNav.classList.add('open');
      overlay.classList.add('open');
      burger.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }

    function closeMob() {
      burger.classList.remove('open');
      mobNav.classList.remove('open');
      overlay.classList.remove('open');
      burger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    burger.addEventListener('click', function () {
      burger.classList.contains('open') ? closeMob() : openMob();
    });

    overlay.addEventListener('click', closeMob);

    mobNav.querySelectorAll('.mob-link').forEach(function (link) {
      link.addEventListener('click', closeMob);
    });
  }

  // ---- Lightbox -------------------------------------------------
  function initLightbox() {
    var lightbox = document.getElementById('lightbox');
    var lightboxImg = document.getElementById('lightboxImg');
    var lightboxClose = document.getElementById('lightboxClose');
    if (!lightbox || !lightboxImg) return;

    function openLightbox(src, alt) {
      lightboxImg.src = src;
      lightboxImg.alt = alt || 'Connector Zoom';
      lightbox.classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
      lightbox.classList.remove('open');
      document.body.style.overflow = '';
      setTimeout(function () {
        lightboxImg.src = '';
      }, 250);
    }

    document.querySelectorAll('.lightbox-trigger, .gallery-stack-item img').forEach(function (el) {
      var trigger = el.classList.contains('lightbox-trigger') ? el : el.closest('.gallery-stack-item');
      if (!trigger) trigger = el;

      trigger.addEventListener('click', function () {
        var src = trigger.getAttribute('data-full') || (el.tagName === 'IMG' ? el.getAttribute('src') : (trigger.querySelector('img') ? trigger.querySelector('img').getAttribute('src') : ''));
        var alt = (el.tagName === 'IMG' ? el.getAttribute('alt') : (trigger.querySelector('img') ? trigger.querySelector('img').getAttribute('alt') : ''));
        if (src) openLightbox(src, alt);
      });

      trigger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          var src = trigger.getAttribute('data-full') || (el.tagName === 'IMG' ? el.getAttribute('src') : (trigger.querySelector('img') ? trigger.querySelector('img').getAttribute('src') : ''));
          var alt = (el.tagName === 'IMG' ? el.getAttribute('alt') : (trigger.querySelector('img') ? trigger.querySelector('img').getAttribute('alt') : ''));
          if (src) openLightbox(src, alt);
        }
      });
    });

    if (lightboxClose) {
      lightboxClose.addEventListener('click', closeLightbox);
    }

    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox || e.target === lightboxClose) {
        closeLightbox();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && lightbox.classList.contains('open')) {
        closeLightbox();
      }
    });
  }

  // ---- Legal Link click handler ---------------------------------
  function initLegalLink() {
    var legalLinks = document.querySelectorAll('a[href$="#hinweise"]');
    var hinweiseTabBtn = document.querySelector('.description-nav-item[data-tab="hinweise"]');
    if (!legalLinks.length || !hinweiseTabBtn) return;

    legalLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        hinweiseTabBtn.click();
        var tabContainer = document.querySelector('.description-section');
        if (tabContainer) {
          tabContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

  // ---- Init on DOM ready ----------------------------------------
  document.addEventListener('DOMContentLoaded', function () {
    initFadeIn();
    initHeader();
    initProductSelector();
    initDescTabs();
    initMobileNav();
    initLegalLink();
    initLightbox();
  });
})();
