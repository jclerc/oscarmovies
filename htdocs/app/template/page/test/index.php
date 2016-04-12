BIENVENUE<br>

<?= $this->flash->build() ?>

<?php if (isset($loginUrl)): ?>
    <a href="<?= e($loginUrl) ?>">Log in with Facebook!</a>
<?php endif; ?>

<?php if (isset($logoutUrl)): ?>
    <a href="<?= e($logoutUrl) ?>">Log out!</a>
<?php endif; ?>

<h2>NOM: <?= $username ?></h2>

<h4>METEO: <?= $weather->main ?> <small>(<?= $weather->description ?>)</small></h4>

<b>Django Unchained</b>: <?= implode(', ', array_keys((array) $this->api->availability->get('Django Unchained'))) ?>
