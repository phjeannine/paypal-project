<?php
print 'Le paiement à bien été effectué '. $args['lastName'] . " " . $args['firstName'] . '<br>' .
  'Voici le mail avec lequel nous vous contacterons : ' . $args['email'] . '<br>' .
  'Voici l\'adresse de livraison : ';

foreach ($args['address'] as $item) {
  echo $item;
  echo '<br>';
}