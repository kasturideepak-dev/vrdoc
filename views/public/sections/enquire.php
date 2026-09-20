<?php
/**
 * Contact details come from _start.php, but site/*.php templates render through
 * get_header() instead and never define them — which left Call / Email / Head
 * office / Hours blank (and logged "Undefined variable $phone1") on those
 * pages. Resolve them here so the section works under either header.
 */
$s = $s ?? ($settings ?? (class_exists('Settings') ? Settings::all() : []));
$phone1 = $phone1 ?? ($s['phone_primary'] ?? '+91 89298 28498');
$phone1tel = $phone1tel ?? preg_replace('/\D+/', '', (string) $phone1);
$phone2 = $phone2 ?? ($s['phone_secondary'] ?? '+91 92569 25643');
$phone2tel = $phone2tel ?? preg_replace('/\D+/', '', (string) $phone2);
?>
<section class="section" id="enquire">
  <div class="container enquire-grid">
    <div>
      <?php if (!empty($c['kicker'])): ?><span class="pill"><?= Html::e($c['kicker']) ?></span><?php endif; ?>
      <h2><?= Html::e($c['heading'] ?? 'Shape your future with VR Doctors') ?></h2>
      <p class="lede"><?= Html::e($c['lede'] ?? '') ?></p>
      <div class="contact-list">
        <p><strong>Call</strong> <a href="tel:+<?= Html::e($phone1tel) ?>"><?= Html::e($phone1) ?></a><br><a href="tel:+<?= Html::e($phone2tel) ?>"><?= Html::e($phone2) ?></a></p>
        <p><strong>Email</strong> <a href="mailto:<?= Html::e($s['email'] ?? '') ?>"><?= Html::e($s['email'] ?? '') ?></a></p>
        <p><strong>Head office</strong> <?= Html::e($s['head_office'] ?? '') ?></p>
        <p><strong>Hours</strong> <?= Html::e($s['hours'] ?? '') ?></p>
      </div>
    </div>
    <div class="enquire-card" id="contact">
      <?php $flash = $_SESSION['_flash'] ?? null; unset($_SESSION['_flash']); ?>
      <?php if ($flash): ?><p><?= Html::e($flash['message']) ?></p><?php endif; ?>
      <form class="form" method="post" action="/enquire/">
        <?= Csrf::field() ?>
        <input type="hidden" name="form" value="enquire">
        <input type="text" name="website" class="visually-hidden" tabindex="-1" autocomplete="off">
        <div class="form-row">
          <label class="field"><input type="text" name="name" placeholder="Name" required></label>
          <label class="field"><input type="tel" name="phone" placeholder="Phone number" required></label>
        </div>
        <label class="field"><input type="email" name="email" placeholder="Email address" required></label>
        <div class="form-row">
          <label class="field"><select name="program" required>
            <option value="">Interested in</option>
            <?php foreach (['MPC — Integrated', 'BiPC — Integrated', 'NEET Long Term'] as $opt): ?>
              <option<?= (($c['program'] ?? '') === $opt) ? ' selected' : '' ?>><?= Html::e($opt) ?></option>
            <?php endforeach; ?>
          </select></label>
          <label class="field"><select name="class" required><option value="">Select class</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option><option>12+</option></select></label>
        </div>
        <label class="field"><select name="branch" required><option value="">Select branch</option><option>Miyapur - Hyderabad</option><option>Madhapur - Hyderabad</option><option>Chandanagar - Hyderabad</option></select></label>
        <label class="field"><textarea name="message" placeholder="Message"></textarea></label>
        <button class="btn" type="submit">Consult today</button>
      </form>
    </div>
  </div>
</section>
