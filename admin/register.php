<?php
// admin/register.php
// Depende de: includes/db_connect.php e admin/config.php

$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');

require __DIR__ . '/../includes/db_connect.php';
session_start();

$err = '';
$val = [
  'nome'     => '',
  'email'    => '',
  'telefone' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome     = trim($_POST['nome'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $telefone = trim($_POST['telefone'] ?? '');
  $senha    = $_POST['senha']  ?? '';
  $senha2   = $_POST['senha2'] ?? '';

  $val['nome'] = $nome;
  $val['email'] = $email;
  $val['telefone'] = $telefone;

  if ($nome === '' || $email === '' || $senha === '' || $senha2 === '') {
    $err = 'Preencha todos os campos obrigatórios.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = 'E-mail inválido.';
  } elseif (strlen($senha) < 6) {
    $err = 'A senha deve ter pelo menos 6 caracteres.';
  } elseif ($senha !== $senha2) {
    $err = 'As senhas não conferem.';
  } else {
    // já existe?
    $st = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = ? LIMIT 1');
    $st->execute([$email]);
    if ($st->fetch()) {
      $err = 'Já existe uma conta com este e-mail.';
    } else {
      // cria usuário cliente
      $hash = password_hash($senha, PASSWORD_DEFAULT);
      // Se você não criou a coluna telefone, remova ", telefone" e ", :telefone" abaixo.
      $ins = $pdo->prepare('
        INSERT INTO usuarios (nome, email, telefone, senha, tipo_usuario, data_cadastro)
        VALUES (:nome, :email, :telefone, :senha, "cliente", NOW())
      ');
      $ins->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':telefone' => $telefone,
        ':senha'    => $hash,
      ]);

      // login do cliente
      $_SESSION['client_id']    = (int)$pdo->lastInsertId();
      $_SESSION['client_email'] = $email;
      $_SESSION['client_name']  = $nome;

      // redireciona para a loja
      header("Location: {$base}/index.php");
      exit;
    }
  }
}

$pageTitle = 'Cadastrar • Doce Encanto';
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cookie&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: { sans:['Poppins','sans-serif'], cookie:['Cookie','cursive'] },
      colors: {
        background:'hsl(35,40%,98%)', foreground:'hsl(330,30%,25%)',
        card:'#fff', border:'hsl(340,30%,90%)',
        primary:'hsl(340,82%,75%)', 'primary-foreground':'#fff',
        ring:'hsl(340,82%,75%)'
      },
      boxShadow: { card:'0 8px 30px rgba(255,182,193,.2)' }
    }
  }
}
</script>
</head>
<body class="bg-background text-foreground min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-xl bg-card rounded-3xl shadow-card border border-border">
    <div class="p-8 md:p-10">
      <div class="text-center mb-6">
        <div class="font-cookie text-3xl text-primary">Doce Encanto</div>
        <h1 class="mt-2 text-xl font-semibold">Crie sua conta</h1>
      </div>

      <?php if ($err): ?>
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
          <?= htmlspecialchars($err) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Nome completo</label>
          <input name="nome" type="text" required value="<?= htmlspecialchars($val['nome']) ?>"
                 class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">E-mail</label>
          <input name="email" type="email" required value="<?= htmlspecialchars($val['email']) ?>"
                 class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Número de celular</label>
          <input name="telefone" type="text" placeholder="(11) 99999-9999" value="<?= htmlspecialchars($val['telefone']) ?>"
                 class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Senha</label>
            <input name="senha" type="password" required
                   class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Confirmar senha</label>
            <input name="senha2" type="password" required
                   class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring">
          </div>
        </div>

        <button type="submit"
                class="w-full mt-2 rounded-full px-6 py-3 font-semibold text-primary-foreground"
                style="background:linear-gradient(135deg,hsl(340,82%,75%) 0%,hsl(340,75%,65%) 100%);">
          Criar conta
        </button>

        <div class="text-center mt-4">
          <a class="text-[hsl(340,75%,45%)] hover:underline" href="<?= $base ?>/admin/login.php">Voltar para o login</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
