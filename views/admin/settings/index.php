<?php
/**
 * Site settings.
 *
 * 38 fields across 8 areas. Presented as anchored panels with a sticky section
 * rail and a sticky save bar, so nothing is more than a click away and saving
 * never requires scrolling to the bottom. Field names are unchanged.
 */
$groups = [
    'identity' => ['Identity', 'Brand, contact details and social links.'],
    'seo' => ['SEO', 'Defaults used when a page has no SEO of its own.'],
    'notfound' => ['404 page', 'What visitors see when a URL does not exist.'],
    'maintenance' => ['Maintenance', 'Take the public site offline temporarily.'],
    'spam' => ['Spam / reCAPTCHA', 'Protect the enquiry forms.'],
    'backups' => ['Backups', 'Automatic database and file snapshots.'],
    'sheets' => ['Google Sheets', 'Mirror form submissions into a spreadsheet.'],
    'smtp' => ['SMTP', 'Outgoing mail for notifications and enquiries.'],
];
?>
<div class="page-head">
  <div>
    <p class="crumbs">System</p>
    <h1>Site settings</h1>
    <p>Brand, SEO defaults, mail and integrations.</p>
  </div>
</div>

<form class="form settings-shell" method="post" enctype="multipart/form-data" id="settings-form">
  <?= Csrf::field() ?>

  <aside class="set-rail" aria-label="Settings sections">
    <nav>
      <?php foreach ($groups as $id => $g): ?>
        <a href="#set-<?= $id ?>" data-set-link="<?= $id ?>"><?= Html::e($g[0]) ?></a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="set-main">

    <section class="panel" id="set-identity">
      <header class="panel-head">
        <h2><?= Html::e($groups['identity'][0]) ?></h2>
        <p><?= Html::e($groups['identity'][1]) ?></p>
      </header>
      <div class="panel-body">
        <div class="field-grid">
          <?php
          $identity = [
            'brand_name' => ['Brand name', 'half'], 'tagline' => ['Tagline', 'half'],
            'logo' => ['Logo path', 'half'], 'favicon' => ['Favicon path', 'half'],
            'phone_primary' => ['Primary phone', 'third'], 'phone_secondary' => ['Secondary phone', 'third'],
            'whatsapp' => ['WhatsApp', 'third'],
            'email' => ['Email (info)', 'half'], 'email_support' => ['Support email', 'half'],
            'hours' => ['Hours', 'half'], 'head_office' => ['Head office', 'half'],
            'brochure' => ['Brochure URL', 'full'],
            'facebook' => ['Facebook', 'half'], 'instagram' => ['Instagram', 'half'],
            'linkedin' => ['LinkedIn', 'half'], 'youtube' => ['YouTube', 'half'],
            'accent_color' => ['Accent colour', 'third'],
          ];
          foreach ($identity as $k => $meta): ?>
            <label class="lab is-<?= $meta[1] ?>"><?= Html::e($meta[0]) ?>
              <input name="<?= $k ?>" value="<?= Html::e($s[$k] ?? '') ?>">
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="panel" id="set-seo">
      <header class="panel-head">
        <h2><?= Html::e($groups['seo'][0]) ?></h2>
        <p><?= Html::e($groups['seo'][1]) ?></p>
      </header>
      <div class="panel-body">
        <div class="field-grid">
          <label class="lab is-half">Default SEO title <input name="default_seo_title" value="<?= Html::e($s['default_seo_title'] ?? '') ?>"></label>
          <label class="lab is-half">Default OG image <input name="og_image" value="<?= Html::e($s['og_image'] ?? '') ?>"></label>
          <label class="lab is-full">Default meta description <textarea name="default_seo_description" rows="2"><?= Html::e($s['default_seo_description'] ?? '') ?></textarea></label>
        </div>
        <?php if (Auth::isSuper()): ?>
          <p class="hint">Tracking scripts (GTM, Analytics, Meta Pixel) and other injected HTML/CSS/JS live in <a href="/admin/snippets/">Code snippets</a> — one place for all site-wide code.</p>
        <?php endif; ?>
        <label class="lab">EducationalOrganization schema (JSON-LD) <textarea name="schema_json" rows="6" spellcheck="false"><?= Html::e($s['schema_json'] ?? '') ?></textarea></label>
        <label class="lab">robots.txt <textarea name="robots_txt" rows="6" spellcheck="false"><?= Html::e($s['robots_txt'] ?? '') ?></textarea></label>
      </div>
    </section>

    <section class="panel" id="set-notfound">
      <header class="panel-head">
        <h2><?= Html::e($groups['notfound'][0]) ?></h2>
        <p><?= Html::e($groups['notfound'][1]) ?></p>
      </header>
      <div class="panel-body">
        <label class="lab">Heading <input name="404_heading" value="<?= Html::e($s['404_heading'] ?? 'Page not found') ?>"></label>
        <label class="lab">Text <textarea name="404_text" rows="2"><?= Html::e($s['404_text'] ?? '') ?></textarea></label>
      </div>
    </section>

    <section class="panel" id="set-maintenance">
      <header class="panel-head">
        <h2><?= Html::e($groups['maintenance'][0]) ?></h2>
        <p><?= Html::e($groups['maintenance'][1]) ?></p>
      </header>
      <div class="panel-body">
        <label class="switch-row">
          <input type="checkbox" name="maintenance_mode" <?= Html::checked(($s['maintenance_mode'] ?? '0') === '1') ?>>
          <span><strong>Enable maintenance mode</strong><small>Visitors get HTTP 503. The admin stays reachable.</small></span>
        </label>
        <label class="lab">Message <input name="maintenance_message" value="<?= Html::e($s['maintenance_message'] ?? '') ?>"></label>
      </div>
    </section>

    <section class="panel" id="set-spam">
      <header class="panel-head">
        <h2><?= Html::e($groups['spam'][0]) ?></h2>
        <p><?= Html::e($groups['spam'][1]) ?></p>
      </header>
      <div class="panel-body">
        <div class="field-grid">
          <label class="lab is-half">Site key <input name="recaptcha_site" value="<?= Html::e($s['recaptcha_site'] ?? '') ?>"></label>
          <label class="lab is-half">Secret <input name="recaptcha_secret" value="<?= Html::e($s['recaptcha_secret'] ?? '') ?>"></label>
        </div>
      </div>
    </section>

    <section class="panel" id="set-backups">
      <header class="panel-head">
        <h2><?= Html::e($groups['backups'][0]) ?></h2>
        <p><?= Html::e($groups['backups'][1]) ?></p>
      </header>
      <div class="panel-body">
        <label class="switch-row">
          <input type="checkbox" name="backup_auto" <?= Html::checked(($s['backup_auto'] ?? '0') === '1') ?>>
          <span><strong>Automatic backups</strong><small>Runs on the scheduled cron.</small></span>
        </label>
        <div class="field-grid">
          <label class="lab is-half">Interval
            <select name="backup_interval">
              <option value="daily"<?= Html::selected($s['backup_interval'] ?? 'daily', 'daily') ?>>Daily</option>
              <option value="weekly"<?= Html::selected($s['backup_interval'] ?? '', 'weekly') ?>>Weekly</option>
            </select>
          </label>
          <label class="lab is-half">Keep last N <input name="backup_retention" value="<?= Html::e($s['backup_retention'] ?? '14') ?>"></label>
        </div>
      </div>
    </section>

    <section class="panel" id="set-sheets">
      <header class="panel-head">
        <h2><?= Html::e($groups['sheets'][0]) ?></h2>
        <p><?= Html::e($groups['sheets'][1]) ?></p>
      </header>
      <div class="panel-body">
        <p class="status-line <?= !empty($sheetsReady) ? 'is-ok' : '' ?>">
          <?= !empty($sheetsReady)
            ? 'Service account key is installed.'
            : 'Upload the JSON key from Google Cloud (enable the Sheets API, then share the sheet with the key’s client_email).' ?>
        </p>
        <label class="lab">Service account JSON <input type="file" name="sheets_json" accept="application/json"></label>
      </div>
    </section>

    <section class="panel" id="set-smtp">
      <header class="panel-head">
        <h2><?= Html::e($groups['smtp'][0]) ?></h2>
        <p><?= Html::e($groups['smtp'][1]) ?></p>
      </header>
      <div class="panel-body">
        <div class="field-grid">
          <label class="lab is-half">SMTP host <input name="smtp_host" value="<?= Html::e($s['smtp_host'] ?? 'smtp.gmail.com') ?>"></label>
          <label class="lab is-quarter">Port <input name="smtp_port" value="<?= Html::e($s['smtp_port'] ?? '587') ?>"></label>
          <label class="lab is-quarter">Encryption
            <select name="smtp_encryption">
              <option value="tls" <?= ($s['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
              <option value="ssl" <?= ($s['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
            </select>
          </label>
          <label class="lab is-half">SMTP user <input type="email" name="smtp_user" value="<?= Html::e($s['smtp_user'] ?? '') ?>"></label>
          <label class="lab is-half">SMTP password
            <input type="password" name="smtp_pass" value="" placeholder="<?= !empty($s['smtp_pass']) ? 'Saved — leave blank to keep' : '' ?>" autocomplete="new-password">
          </label>
          <label class="lab is-half">From email <input type="email" name="smtp_from_email" value="<?= Html::e($s['smtp_from_email'] ?? '') ?>"></label>
          <label class="lab is-half">From name <input name="smtp_from_name" value="<?= Html::e($s['smtp_from_name'] ?? '') ?>"></label>
        </div>
      </div>
    </section>

  </div>

  <div class="save-bar">
    <span class="save-bar__note" data-dirty-note hidden>Unsaved changes</span>
    <button class="btn" type="submit">Save settings</button>
  </div>
</form>

<form class="panel panel--inline" method="post" action="/admin/settings/smtp-test/">
  <?= Csrf::field() ?>
  <div class="panel-body">
    <h2>Send a test email</h2>
    <p class="hint">Confirms the SMTP details above actually work. Save first.</p>
    <div class="field-grid">
      <label class="lab is-half">Send to <input type="email" name="test_email" value="<?= Html::e($s['smtp_user'] ?? '') ?>"></label>
      <div class="lab is-half" style="align-self:end">
        <button class="btn-ghost" type="submit">Send test email</button>
      </div>
    </div>
  </div>
</form>
