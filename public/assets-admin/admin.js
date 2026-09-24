(function () {
  var csrf = document.querySelector('meta[name="csrf-token"]');
  csrf = csrf ? csrf.getAttribute("content") : "";
  var toast = document.getElementById("toast");

  function showToast(msg, ok) {
    if (!toast) return;
    toast.textContent = msg;
    toast.style.borderColor = ok === false ? "rgba(255,107,107,.4)" : "rgba(61,220,151,.35)";
    toast.classList.add("is-on");
    setTimeout(function () { toast.classList.remove("is-on"); }, 2400);
  }

  document.querySelectorAll("[data-confirm]").forEach(function (el) {
    el.addEventListener("submit", function (e) {
      if (!confirm(el.getAttribute("data-confirm") || "Are you sure?")) e.preventDefault();
    });
  });

  document.querySelectorAll("[data-ajax]").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      if (form.getAttribute("data-ajax") === "off") return;
      e.preventDefault();
      var fd = new FormData(form);
      fd.append("ajax", "1");
      var btn = form.querySelector("[type=submit]");
      if (btn) btn.disabled = true;
      fetch(form.action || location.href, {
        method: "POST",
        body: fd,
        headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": csrf, "Accept": "application/json" }
      }).then(function (r) { return r.json().catch(function () { return { ok: false, error: "Save failed" }; }); })
        .then(function (j) {
          if (btn) btn.disabled = false;
          if (j.redirect && !j.message) { location.href = j.redirect; return; }
          showToast(j.message || j.error || (j.ok ? "Saved" : "Could not save"), j.ok !== false);
          if (j.redirect && j.ok) setTimeout(function () { location.href = j.redirect; }, 400);
        })
        .catch(function () {
          if (btn) btn.disabled = false;
          showToast("Network error", false);
        });
    });
  });

  document.querySelectorAll("[data-slug-source]").forEach(function (src) {
    var target = document.querySelector(src.getAttribute("data-slug-source"));
    if (!target) return;
    var locked = target.value !== "";
    target.addEventListener("input", function () { locked = true; });
    src.addEventListener("input", function () {
      if (locked) return;
      target.value = src.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");
    });
  });

  var slugInput = document.querySelector("[data-slug-check]");
  if (slugInput) {
    var timer;
    slugInput.addEventListener("input", function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        var u = "/admin/api/slug-check/?kind=" + encodeURIComponent(slugInput.getAttribute("data-slug-check"))
          + "&slug=" + encodeURIComponent(slugInput.value)
          + "&id=" + encodeURIComponent(slugInput.getAttribute("data-id") || "")
          + "&type_id=" + encodeURIComponent(slugInput.getAttribute("data-type-id") || "");
        fetch(u, { headers: { "X-Requested-With": "XMLHttpRequest" } })
          .then(function (r) { return r.json(); })
          .then(function (j) {
            slugInput.style.borderColor = j.ok ? "" : "var(--err)";
            var hint = slugInput.parentElement.querySelector(".slug-hint");
            if (!hint) {
              hint = document.createElement("small");
              hint.className = "slug-hint";
              hint.style.color = "var(--err)";
              slugInput.parentElement.appendChild(hint);
            }
            hint.textContent = j.ok ? "" : (j.message || "Unavailable");
          });
      }, 280);
    });
  }

  function bindSortable(list) {
    if (!list) return;
    var drag;
    var items = function () { return list.querySelectorAll(list.getAttribute("data-sort-item") || ".pe-item, .sec"); };
    items().forEach(function (sec) {
      sec.addEventListener("dragstart", function (e) {
        if (e.target.closest("button, input, textarea, select, a")) return;
        drag = sec; sec.classList.add("is-drag");
        // Firefox will not begin a drag unless some data is set.
        if (e.dataTransfer) { e.dataTransfer.effectAllowed = "move"; try { e.dataTransfer.setData("text/plain", ""); } catch (err) {} }
      });
      sec.addEventListener("dragend", function () {
        sec.classList.remove("is-drag");
        var url = list.getAttribute("data-reorder") || (list.closest("[data-reorder]") && list.closest("[data-reorder]").getAttribute("data-reorder"));
        if (!url) return;
        var order = [].map.call(list.querySelectorAll("[name='section_id[]']"), function (i) { return i.value; });
        var fd = new FormData();
        fd.append("_csrf", csrf);
        fd.append("ajax", "1");
        order.forEach(function (id) { fd.append("order[]", id); });
        fetch(url, { method: "POST", body: fd, headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": csrf, "Accept": "application/json" } })
          .then(function () {
            [].forEach.call(list.querySelectorAll(".pe-num"), function (n, i) { n.textContent = String(i + 1); });
            showToast("Section order saved", true);
          });
      });
      sec.addEventListener("dragover", function (e) {
        e.preventDefault();
        if (!drag || drag === sec) return;
        var rect = sec.getBoundingClientRect();
        var before = (e.clientY - rect.top) < rect.height / 2;
        list.insertBefore(drag, before ? sec : sec.nextSibling);
      });
    });
  }
  bindSortable(document.querySelector("[data-sortable]"));
  bindSortable(document.querySelector("[data-sec-list]"));

  document.querySelectorAll("[data-add-field]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var wrap = document.querySelector(btn.getAttribute("data-add-field"));
      if (!wrap) return;
      var proto = wrap.getAttribute("data-proto");
      if (!proto) return;
      wrap.insertAdjacentHTML("beforeend", proto);
    });
  });

  // Settings: highlight the rail link for whichever panel is in view, and flag
  // unsaved changes in the sticky save bar.
  (function () {
    var shell = document.getElementById("settings-form");
    if (!shell) return;
    var links = [].slice.call(shell.querySelectorAll("[data-set-link]"));
    var panels = links
      .map(function (a) { return document.getElementById("set-" + a.getAttribute("data-set-link")); })
      .filter(Boolean);
    if (!panels.length) return;

    function mark(id) {
      links.forEach(function (a) {
        a.classList.toggle("is-current", a.getAttribute("data-set-link") === id);
      });
    }
    mark(links[0].getAttribute("data-set-link"));

    if ("IntersectionObserver" in window) {
      var seen = {};
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) { seen[e.target.id] = e.isIntersecting ? e.intersectionRatio : 0; });
        var best = null, bestRatio = 0;
        panels.forEach(function (p) {
          var r = seen[p.id] || 0;
          if (r > bestRatio) { bestRatio = r; best = p; }
        });
        if (best) mark(best.id.replace(/^set-/, ""));
      }, { rootMargin: "-88px 0px -55% 0px", threshold: [0, 0.25, 0.5, 1] });
      panels.forEach(function (p) { io.observe(p); });
    }

    links.forEach(function (a) {
      a.addEventListener("click", function () { mark(a.getAttribute("data-set-link")); });
    });

    var note = shell.querySelector("[data-dirty-note]");
    if (note) {
      shell.addEventListener("input", function () { note.hidden = false; }, { once: true });
      shell.addEventListener("submit", function () { note.hidden = true; });
    }
  })();

  // Bulk selection on entry lists.
  (function () {
    var form = document.getElementById("bulk-form");
    if (!form) return;
    var all = form.querySelector("[data-bulk-all]");
    var bar = form.querySelector("[data-bulk-bar]");
    var count = form.querySelector("[data-bulk-count]");
    function rows() { return [].slice.call(form.querySelectorAll("[data-bulk-row]")); }
    function sync() {
      var picked = rows().filter(function (c) { return c.checked; });
      if (count) count.textContent = String(picked.length);
      if (bar) bar.hidden = picked.length === 0;
      if (all) {
        all.checked = picked.length > 0 && picked.length === rows().length;
        all.indeterminate = picked.length > 0 && picked.length < rows().length;
      }
    }
    if (all) {
      all.addEventListener("change", function () {
        rows().forEach(function (c) { c.checked = all.checked; });
        sync();
      });
    }
    form.addEventListener("change", function (e) {
      if (e.target.matches("[data-bulk-row]")) sync();
    });
    form.addEventListener("submit", function (e) {
      var picked = rows().filter(function (c) { return c.checked; }).length;
      var act = form.querySelector("[name=bulk_action]");
      if (!picked) { e.preventDefault(); showToast("Select at least one entry", false); return; }
      if (act && (act.value === "delete" || act.value === "trash")) {
        var verb = act.value === "delete" ? "permanently delete" : "move to trash";
        if (!confirm("Really " + verb + " " + picked + " entr" + (picked === 1 ? "y" : "ies") + "?")) {
          e.preventDefault();
        }
      }
    });
    sync();
  })();

  // Entry editor behaviours.
  (function () {
    var form = document.getElementById("entry-form");
    if (!form) return;
    var status = form.querySelector("[data-status]");
    var sched = form.querySelector("[data-schedule-row]");
    function syncSchedule() { if (sched && status) sched.hidden = status.value !== "scheduled"; }
    if (status) status.addEventListener("change", syncSchedule);
    syncSchedule();

    var pub = form.querySelector("[data-save-publish]");
    if (pub && status) {
      pub.addEventListener("click", function () {
        status.value = "published";
        syncSchedule();
        if (form.requestSubmit) form.requestSubmit(); else form.submit();
      });
    }

    // New entry: Body only applies when starting Blank; a template builds the
    // page from sections, which is where its text goes.
    var body = form.querySelector("[data-body-block]");
    var tplRadios = [].slice.call(form.querySelectorAll("input[name=template_id]"));
    if (body && tplRadios.length) {
      var syncBody = function () {
        var picked = tplRadios.filter(function (r) { return r.checked; })[0];
        body.hidden = !!(picked && picked.value);
      };
      tplRadios.forEach(function (r) { r.addEventListener("change", syncBody); });
      syncBody();
    }

    var toggle = form.querySelector("[data-sec-toggle-all]");
    if (toggle) {
      toggle.addEventListener("click", function () {
        var cards = [].slice.call(form.querySelectorAll(".sec-card"));
        var open = cards.some(function (c) { return !c.open; });
        cards.forEach(function (c) { c.open = open; });
        toggle.textContent = open ? "Collapse all" : "Expand all";
      });
    }

    var list = form.querySelector(".sec-list");
    if (list) {
      list.addEventListener("dragend", function () {
        [].forEach.call(list.querySelectorAll(".sec-card"), function (c, i) {
          var n = c.querySelector(".sec-card__num");
          if (n) n.textContent = String(i + 1);
        });
        showToast("Order changed — save to keep it", true);
      });
    }
  })();

  // Light / dark theme switch, remembered per browser.
  (function () {
    var btn = document.querySelector("[data-theme-toggle]");
    if (!btn) return;
    btn.addEventListener("click", function () {
      var root = document.documentElement;
      var dark = root.getAttribute("data-theme") !== "dark";
      if (dark) root.setAttribute("data-theme", "dark"); else root.removeAttribute("data-theme");
      try { localStorage.setItem("vr-admin-theme", dark ? "dark" : "light"); } catch (e) {}
    });
  })();

  // Menu editor: drag to reorder, live summaries, custom-URL field toggle.
  (function () {
    var form = document.getElementById("menu-form");
    if (!form) return;

    // "Links to" → show the URL box only for custom links.
    function syncTarget(sel) {
      var scope = sel.closest(".menu-row__body, .panel-body");
      var box = scope && scope.querySelector("[data-custom-url]");
      if (box) box.hidden = sel.value !== "custom";
      var row = sel.closest("[data-menu-row]");
      if (row && sel.value !== "custom") {
        var url = row.querySelector("[data-row-url]");
        var opt = sel.options[sel.selectedIndex];
        if (url && opt) url.textContent = "→ " + opt.textContent.trim();
      }
    }
    [].forEach.call(form.querySelectorAll("[data-target-select]"), function (sel) {
      sel.addEventListener("change", function () { syncTarget(sel); });
      syncTarget(sel);
    });

    // Label edits update the collapsed summary straight away.
    form.addEventListener("input", function (e) {
      if (!e.target.matches("[data-label-input]")) return;
      var row = e.target.closest("[data-menu-row]");
      var lab = row && row.querySelector("[data-row-label]");
      if (lab) lab.textContent = e.target.value || "Untitled";
    });

    // Drag a group (a link plus its dropdown) within its own list only.
    var dragging = null;
    form.addEventListener("dragstart", function (e) {
      var grip = e.target.closest && e.target.closest(".menu-row__grip");
      if (!grip) return;
      dragging = grip.closest("[data-menu-group]");
      dragging.classList.add("is-drag");
      if (e.dataTransfer) { e.dataTransfer.effectAllowed = "move"; try { e.dataTransfer.setData("text/plain", ""); } catch (err) {} }
    });
    form.addEventListener("dragover", function (e) {
      if (!dragging) return;
      var over = e.target.closest && e.target.closest("[data-menu-group]");
      if (!over || over === dragging || over.parentNode !== dragging.parentNode) return;
      e.preventDefault();
      var r = over.getBoundingClientRect();
      over.parentNode.insertBefore(dragging, (e.clientY - r.top) < r.height / 2 ? over : over.nextSibling);
    });
    form.addEventListener("dragend", function () {
      if (!dragging) return;
      dragging.classList.remove("is-drag");
      dragging = null;
      showToast("Order changed — Save menu to keep it", true);
    });
  })();

  var picker = document.getElementById("media-picker");
  var pickerRows = [];
  function renderPicker(q) {
    var grid = picker && picker.querySelector(".media-grid");
    if (!grid) return;
    q = (q || "").toLowerCase();
    var rows = pickerRows.filter(function (m) {
      if (!q) return true;
      return (m.original_name || "").toLowerCase().indexOf(q) !== -1 || (m.alt_text || "").toLowerCase().indexOf(q) !== -1;
    });
    grid.innerHTML = rows.map(function (m) {
      var dim = (m.width && m.height) ? (m.width + "×" + m.height) : "";
      var kb = m.size_bytes ? (Math.round(m.size_bytes / 1024) + " KB") : "";
      var src = (m.mime || "").indexOf("image/") === 0 ? m.public_path : "";
      return '<figure data-pick="' + m.public_path + '">' +
        (src ? '<img src="' + src + '" alt="">' : '<div class="empty">File</div>') +
        '<figcaption>' + (m.original_name || "") + '<br>' + [dim, kb].filter(Boolean).join(" · ") + "</figcaption></figure>";
    }).join("") || '<p class="empty">No matching files.</p>';
  }
  var pickCallback = null;
  // Open the library for a caller that wants the chosen path back directly.
  window.vrOpenMediaPicker = function (cb) {
    if (!picker) return;
    pickCallback = cb;
    picker.dataset.target = "";
    picker.classList.add("is-on");
    var st = picker.querySelector("[data-media-status]");
    if (st) { st.hidden = true; st.textContent = ""; }
    fetch("/admin/media/?ajax=1", { headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" } })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        pickerRows = j.rows || [];
        var q = picker.querySelector("[data-media-search]");
        renderPicker(q ? q.value : "");
      });
  };
  function applyPicked(path) {
    if (pickCallback) { var cb = pickCallback; pickCallback = null; cb(path); return; }
    var sel = picker.dataset.target;
    var input = sel ? document.querySelector(sel) : null;
    if (!input) return;
    input.value = path;
    input.dispatchEvent(new Event("input", { bubbles: true }));
    var wrap = input.closest("[data-img-field]");
    if (wrap) {
      var img = wrap.querySelector("img");
      var empty = wrap.querySelector(".img-field__empty");
      var pathEl = wrap.querySelector(".img-field__path");
      var clear = wrap.querySelector("[data-img-clear]");
      if (img) { img.src = path; img.hidden = !path; }
      if (empty) empty.hidden = !!path;
      if (pathEl) pathEl.textContent = path ? path.split("/").pop() : "None selected";
      if (clear) clear.hidden = !path;
    }
  }
  document.body.addEventListener("click", function (e) {
    var t = e.target.closest("[data-media-open]");
    if (!t || !picker) return;
    e.preventDefault();
    picker.dataset.target = t.getAttribute("data-media-open");
    picker.classList.add("is-on");
    var st = picker.querySelector("[data-media-status]");
    if (st) { st.hidden = true; st.textContent = ""; }
    fetch("/admin/media/?ajax=1", { headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" } })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        pickerRows = j.rows || [];
        var q = picker.querySelector("[data-media-search]");
        renderPicker(q ? q.value : "");
      });
  });
  if (picker) {
    var qin = picker.querySelector("[data-media-search]");
    if (qin) qin.addEventListener("input", function () { renderPicker(qin.value); });

    // Upload straight from the picker. Without this the only way to add an
    // image mid-edit was to leave the form, go to Media, upload, and come back.
    var up = picker.querySelector("[data-media-upload]");
    var status = picker.querySelector("[data-media-status]");
    function say(msg, bad) {
      if (!status) return;
      status.hidden = !msg;
      status.textContent = msg || "";
      status.classList.toggle("is-error", !!bad);
    }
    if (up) {
      up.addEventListener("change", function () {
        var files = up.files;
        if (!files || !files.length) return;
        var fd = new FormData();
        fd.append("_csrf", csrf);
        for (var i = 0; i < files.length; i++) fd.append("files[]", files[i]);
        say("Uploading " + files.length + " file" + (files.length > 1 ? "s" : "") + "…");
        fetch("/admin/media/", {
          method: "POST",
          body: fd,
          headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": csrf, "Accept": "application/json" }
        })
          .then(function (r) { return r.json().catch(function () { return { ok: false, error: "Upload failed" }; }); })
          .then(function (j) {
            up.value = "";
            if (!j.ok || !j.files || !j.files.length) {
              say(j.error || "Nothing was uploaded — check the file type.", true);
              return;
            }
            pickerRows = j.files.concat(pickerRows);
            if (qin) qin.value = "";
            renderPicker("");
            say(j.files.length + " uploaded. Selecting " + (j.files[0].original_name || "file") + "…");
            // Single file is almost always "I want this one" — apply and close.
            if (j.files.length === 1) {
              applyPicked(j.files[0].public_path);
              picker.classList.remove("is-on");
              say("");
              showToast("Uploaded and selected", true);
            }
          })
          .catch(function () { up.value = ""; say("Network error during upload.", true); });
      });
    }
    picker.addEventListener("click", function (e) {
      if (e.target.closest("[data-media-upload], .media-upload-btn")) return;
    });
    picker.addEventListener("click", function (e) {
      if (e.target === picker || e.target.closest("[data-media-close]")) { picker.classList.remove("is-on"); pickCallback = null; }
      var fig = e.target.closest("[data-pick]");
      if (!fig) return;
      applyPicked(fig.getAttribute("data-pick"));
      picker.classList.remove("is-on");
    });
  }
  document.body.addEventListener("click", function (e) {
    var c = e.target.closest("[data-img-clear]");
    if (!c) return;
    var wrap = c.closest("[data-img-field]");
    if (!wrap) return;
    var input = wrap.querySelector("input[id]");
    if (input && picker) picker.dataset.target = "#" + input.id;
    applyPicked("");
  });

  (function pageEditor() {
    var root = document.querySelector("[data-page-editor]");
    if (!root) return;
    function selectSec(id) {
      root.querySelectorAll(".pe-item").forEach(function (el) {
        el.classList.toggle("is-on", el.getAttribute("data-sec-id") === String(id));
      });
      root.querySelectorAll(".pe-panel").forEach(function (el) {
        var on = el.getAttribute("data-panel") === String(id);
        el.hidden = !on;
        el.classList.toggle("is-on", on);
      });
      var focus = document.getElementById("focus-sec");
      if (focus) focus.value = String(id);
      var url = new URL(location.href);
      url.searchParams.set("sec", String(id));
      history.replaceState(null, "", url);
    }
    root.addEventListener("click", function (e) {
      var sel = e.target.closest("[data-select-sec]");
      if (sel) selectSec(sel.getAttribute("data-select-sec"));
      var vis = e.target.closest("[data-vis-toggle]");
      if (vis) {
        e.preventDefault();
        e.stopPropagation();
        var id = vis.getAttribute("data-vis-toggle");
        var field = root.querySelector("[data-vis-field='" + id + "']");
        var next = field && field.value === "1" ? "0" : "1";
        if (field) field.value = next;
        var item = vis.closest(".pe-item");
        if (item) item.classList.toggle("is-hidden", next === "0");
        vis.textContent = next === "1" ? "◉" : "○";
        vis.setAttribute("aria-pressed", next === "1" ? "true" : "false");
        vis.title = next === "1" ? "Hide section" : "Show section";
        var fd = new FormData();
        fd.append("_csrf", csrf);
        fd.append("section_id", id);
        fd.append("visible", next);
        fetch(root.getAttribute("data-visible"), {
          method: "POST", body: fd,
          headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": csrf, "Accept": "application/json" }
        }).then(function (r) { return r.json(); }).then(function (j) {
          showToast(next === "1" ? "Section visible" : "Section hidden", j.ok !== false);
        });
      }
    });
    root.querySelectorAll(".pe-panel").forEach(function (panel) {
      panel.addEventListener("input", function () {
        var id = panel.getAttribute("data-panel");
        var item = root.querySelector(".pe-item[data-sec-id='" + id + "']");
        if (item) {
          var d = item.querySelector(".pe-dirty");
          if (d) d.hidden = false;
        }
        var hint = root.querySelector("[data-dirty-hint]");
        if (hint) hint.hidden = false;
      });
    });
    var prev = root.querySelector("[data-save-preview]");
    if (prev) prev.addEventListener("click", function () {
      document.getElementById("save-after").value = "preview";
    });
    var save = root.querySelector("[data-save]");
    if (save) save.addEventListener("click", function () {
      document.getElementById("save-after").value = "";
    });
    document.querySelectorAll("[data-rev-open]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var art = btn.closest(".pe-rev");
        var open = art.classList.contains("is-open");
        document.querySelectorAll(".pe-rev").forEach(function (a) {
          a.classList.remove("is-open");
          var d = a.querySelector(".pe-rev__detail");
          if (d) d.hidden = true;
        });
        if (!open) {
          art.classList.add("is-open");
          var det = art.querySelector(".pe-rev__detail");
          if (det) det.hidden = false;
          var acc = document.getElementById("revisions-acc");
          if (acc) acc.open = true;
        }
      });
    });
  })();

  (function initWysiwyg() {
    if (typeof Quill === "undefined") return;
    var editors = [];

    // What the editor stores. Quill 2's raw innerHTML writes bullet lists as
    // <ol><li data-list="bullet">, which the site renders as numbered lists;
    // getSemanticHTML() emits real <ul>/<ol>. 2.0.2 also turns every space into
    // &nbsp; there (fixed upstream in 2.0.3), which would stop text wrapping.
    function editorHTML(quill) {
      var html = typeof quill.getSemanticHTML === "function" ? quill.getSemanticHTML() : quill.root.innerHTML;
      html = html.replace(/&nbsp;/g, " ");
      return html === "<p></p>" || html === "<p><br></p>" ? "" : html;
    }

    // Upload image files to the media library and hand back their public paths.
    function uploadImages(files) {
      var fd = new FormData();
      fd.append("_csrf", csrf);
      [].forEach.call(files, function (f) { fd.append("files[]", f); });
      return fetch("/admin/media/", {
        method: "POST",
        body: fd,
        headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": csrf, "Accept": "application/json" }
      }).then(function (r) { return r.json(); }).then(function (j) {
        if (!j.ok || !j.files || !j.files.length) throw new Error(j.error || "Upload failed");
        return j.files.map(function (f) { return f.public_path; });
      });
    }

    var UNDO_ICON = '<svg viewBox="0 0 18 18"><path class="ql-stroke" d="M5 7H11a4 4 0 0 1 0 8H7"/><path class="ql-stroke" d="M7.5 4.5L5 7l2.5 2.5"/></svg>';
    var REDO_ICON = '<svg viewBox="0 0 18 18"><path class="ql-stroke" d="M13 7H7a4 4 0 0 0 0 8h4"/><path class="ql-stroke" d="M10.5 4.5L13 7l-2.5 2.5"/></svg>';

    document.querySelectorAll(".wysiwyg-mount[data-wysiwyg-for]").forEach(function (mount) {
      var id = mount.getAttribute("data-wysiwyg-for");
      var ta = document.getElementById(id);
      if (!ta || mount.dataset.quillReady) return;
      mount.dataset.quillReady = "1";

      var quill = new Quill(mount, {
        theme: "snow",
        placeholder: "Start writing… drag an image in, or paste one.",
        modules: {
          history: { delay: 800, maxStack: 200, userOnly: true },
          toolbar: {
            container: [
              [{ header: [2, 3, 4, false] }],
              ["bold", "italic", "underline", "strike"],
              [{ color: [] }, { background: [] }],
              [{ align: [] }],
              [{ list: "ordered" }, { list: "bullet" }, { indent: "-1" }, { indent: "+1" }],
              ["blockquote", "code-block"],
              ["link", "image", "video"],
              ["undo", "redo"],
              ["clean"]
            ],
            handlers: {
              undo: function () { this.quill.history.undo(); },
              redo: function () { this.quill.history.redo(); },
              // The image button opens the media library (with its upload
              // button) instead of asking for a raw URL.
              image: function () {
                var q = this.quill;
                var range = q.getSelection(true);
                if (typeof window.vrOpenMediaPicker !== "function") return;
                window.vrOpenMediaPicker(function (path) {
                  var at = range ? range.index : q.getLength();
                  q.insertEmbed(at, "image", path, "user");
                  q.setSelection(at + 1, 0, "silent");
                });
              }
            }
          },
          // Dropped or pasted image files. Quill's default inlines them as
          // base64 data: URIs — megabytes of text in the database. Send them to
          // the media library instead and embed the real file path.
          uploader: {
            mimetypes: ["image/png", "image/jpeg", "image/gif", "image/webp", "image/svg+xml"],
            handler: function (range, files) {
              var q = this.quill;
              wrap.classList.add("is-uploading");
              showToast("Uploading " + files.length + " image" + (files.length > 1 ? "s" : "") + "…", true);
              uploadImages(files).then(function (paths) {
                var at = range ? range.index : q.getLength();
                paths.forEach(function (p, i) { q.insertEmbed(at + i, "image", p, "user"); });
                q.setSelection(at + paths.length, 0, "silent");
                showToast("Image added to the post and the media library", true);
              }).catch(function (err) {
                showToast(err.message || "Upload failed", false);
              }).then(function () { wrap.classList.remove("is-uploading"); });
            }
          }
        }
      });

      var wrap = mount.closest(".wysiwyg-field") || mount.parentNode;
      var toolbar = wrap.querySelector(".ql-toolbar");
      if (toolbar) {
        var u = toolbar.querySelector(".ql-undo"); if (u) { u.innerHTML = UNDO_ICON; u.title = "Undo (Ctrl/⌘ Z)"; }
        var r = toolbar.querySelector(".ql-redo"); if (r) { r.innerHTML = REDO_ICON; r.title = "Redo (Ctrl/⌘ ⇧ Z)"; }
      }

      // Visual cue while a file is dragged over the writing area.
      ["dragenter", "dragover"].forEach(function (ev) {
        quill.root.addEventListener(ev, function (e) {
          if (e.dataTransfer && [].indexOf.call(e.dataTransfer.types || [], "Files") !== -1) wrap.classList.add("is-dropping");
        });
      });
      ["dragleave", "drop"].forEach(function (ev) {
        quill.root.addEventListener(ev, function () { wrap.classList.remove("is-dropping"); });
      });

      // Footer: live word count and a drag handle to resize the writing area.
      var foot = document.createElement("div");
      foot.className = "wysiwyg-foot";
      foot.innerHTML = '<span class="wysiwyg-count"></span><span class="wysiwyg-grip" title="Drag to resize" aria-hidden="true"></span>';
      mount.parentNode.insertBefore(foot, mount.nextSibling);
      var count = foot.querySelector(".wysiwyg-count");
      function recount() {
        var words = quill.getText().trim().split(/\s+/).filter(Boolean).length;
        count.textContent = words + " word" + (words === 1 ? "" : "s");
      }
      var grip = foot.querySelector(".wysiwyg-grip");
      grip.addEventListener("pointerdown", function (e) {
        e.preventDefault();
        var startY = e.clientY, startH = mount.getBoundingClientRect().height;
        grip.setPointerCapture(e.pointerId);
        wrap.classList.add("is-resizing");
        function move(ev) { mount.style.height = Math.max(140, Math.min(1600, startH + ev.clientY - startY)) + "px"; }
        function up() {
          grip.removeEventListener("pointermove", move);
          grip.removeEventListener("pointerup", up);
          wrap.classList.remove("is-resizing");
        }
        grip.addEventListener("pointermove", move);
        grip.addEventListener("pointerup", up);
      });
      grip.addEventListener("dblclick", function () { mount.style.height = ""; });

      editors.push({ quill: quill, ta: ta });
      quill.on("text-change", function () {
        ta.value = editorHTML(quill);
        recount();
      });
      recount();
    });
    function syncAll() {
      editors.forEach(function (pair) { pair.ta.value = editorHTML(pair.quill); });
    }
    document.querySelectorAll("form").forEach(function (form) {
      form.addEventListener("submit", syncAll);
    });
  })();

  var drop = document.querySelector("[data-drop-upload]");
  if (drop) {
    ["dragenter", "dragover"].forEach(function (ev) {
      drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add("is-on"); });
    });
    drop.addEventListener("dragleave", function () { drop.classList.remove("is-on"); });
    drop.addEventListener("drop", function (e) {
      e.preventDefault();
      drop.classList.remove("is-on");
      var input = drop.querySelector("input[type=file]");
      if (!input) return;
      input.files = e.dataTransfer.files;
      drop.closest("form").requestSubmit();
    });
  }

  /* ——— Blog FAQ editor: add / remove question rows ——— */
  (function () {
    var editor = document.querySelector("[data-faq-editor]");
    if (!editor) return;
    var rows = editor.querySelector("[data-faq-rows]");
    var empty = editor.querySelector("[data-faq-empty]");
    function sync() {
      if (empty) empty.hidden = rows.children.length > 0;
    }
    function addRow() {
      var row = document.createElement("div");
      row.className = "faq-row";
      row.setAttribute("data-faq-row", "");
      row.innerHTML =
        '<span class="faq-row__grip" aria-hidden="true">\u22ee\u22ee</span>' +
        '<div class="faq-row__fields">' +
        '<label class="lab">Question <input name="faq_q[]" placeholder="e.g. Who can apply for this programme?"></label>' +
        '<label class="lab">Answer <textarea name="faq_a[]" rows="3" placeholder="Keep it short and direct."></textarea></label>' +
        "</div>" +
        '<button class="act act--danger" type="button" data-faq-remove title="Remove this question">Remove</button>';
      rows.appendChild(row);
      sync();
      var input = row.querySelector("input");
      if (input) input.focus();
    }
    var addBtn = editor.querySelector("[data-faq-add]");
    if (addBtn) addBtn.addEventListener("click", addRow);
    editor.addEventListener("click", function (e) {
      var rm = e.target.closest("[data-faq-remove]");
      if (!rm) return;
      var row = rm.closest("[data-faq-row]");
      if (row) row.remove();
      sync();
    });
    sync();
  })();

})();
