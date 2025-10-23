<?php
return [
  // Credenciais do dono
  'owner_email'      => 'admin@doceencanto.com', // use exatamente este e-mail no login
  'owner_user'       => 'admin',                 // também aceitaremos "admin" no campo de e-mail/usuário

  // Senha: 123456 (troque depois!)
  'owner_pass_hash'  => '$2y$10$ZQd2tNPn8dQa.NjUj6H8hOtG2n0TqD3Gxhxk1c9Ew0pQx7H0x7c1G',

  // Caminho base do site (ajuste se sua pasta for outra)
  'base'             => '/pastel-sweet-shop',

  // ======= MODO DEV (facilita destravar login local) =======
  // Quando true, além do hash, também aceita a senha em texto abaixo.
  'dev_allow_plain'  => true,
  'owner_pass_plain' => '123456',
];
