BIENVENUE<br>

<?php

  $fb = new Facebook\Facebook([
  'app_id' => '1372343316125328', // Replace {app-id} with your app id
  'app_secret' => 'a1f81f07cfc0e5e9e2958e5131fd2d6b',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://localhost:8888/facebook/connect/', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';

?>

NOM: <?= $name ?>
