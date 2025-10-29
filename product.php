<?php
session_start();
require 'db-connect.php'; // ← PDO接続用ファイル (例: $pdo = new PDO(...))

// 商品IDを受け取る
$id = $_GET['id'] ?? 0;

// 商品情報取得
$sql = $pdo->prepare('SELECT * FROM items WHERE id = ?');
$sql->execute([$id]);
$item = $sql->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo '商品が見つかりません。';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($item['name']) ?> | SATONOMI</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
<style>
body {
  background-color: #eee;
  font-family: 'Hiragino Sans', 'Noto Sans JP', sans-serif;
}
.header {
  background-color: #fdf4e3;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
}
.logo {
  display: flex;
  align-items: center;
  font-weight: bold;
}
.logo img {
  width: 40px;
  margin-right: 8px;
}
.icon {
  font-size: 1.5rem;
}
.card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  margin: 30px auto;
  padding: 20px;
  width: 90%;
  max-width: 450px;
}
.price-box {
  font-size: 1rem;
  margin-top: 10px;
}
.counter {
  display: flex;
  align-items: center;
  gap: 8px;
}
.counter button {
  border: none;
  background: #dff4e1;
  border-radius: 50%;
  width: 25px;
  height: 25px;
  cursor: pointer;
}
.cart-btn {
  background: #a8f0c1;
  color: #333;
  font-weight: bold;
  border: none;
  border-radius: 50px;
  padding: 12px 0;
  width: 100%;
  margin-top: 20px;
  cursor: pointer;
  transition: 0.3s;
}
.cart-btn:hover {
  background: #8ce0ab;
}
.like {
  color: #666;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 6px;
}
.details {
  margin-top: 20px;
}
</style>
</head>
<body>

<!-- ヘッダー -->
<div class="header">
  <div class="logo">
    <img src="logo_satonomi.png" alt="SATONOMIロゴ">
    SATONOMI
  </div>
  <div>
    <span class="icon">🛒</span>
    <span class="icon">👤</span>
  </div>
</div>

<!-- 商品情報 -->
<div class="card">
  <div style="text-align:center;">
    <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="max-width:200px;">
  </div>

  <p style="color:#999; font-size:0.9em; margin-top:10px;"><?= htmlspecialchars($item['company']) ?></p>
  <h2 style="font-weight:bold; font-size:1.2em;"><?= htmlspecialchars($item['name']) ?>（<?= htmlspecialchars($item['volume']) ?>）</h2>

  <div class="like">❤️ <?= htmlspecialchars($item['likes']) ?></div>

  <form action="cart.php" method="post">
    <div class="price-box">
      1本 / ￥<?= number_format($item['price']) ?>（税込）  
      <div class="counter">
        <button type="button" onclick="changeCount('single', -1)">−</button>
        <span id="count-single">0</span>
        <button type="button" onclick="changeCount('single', 1)">＋</button>
      </div>
    </div>

    <div class="price-box">
      <?= htmlspecialchars($item['volume']) ?>×24本 / ￥<?= number_format($item['price'] * 24) ?>（税込）  
      <div class="counter">
        <button type="button" onclick="changeCount('case', -1)">−</button>
        <span id="count-case">0</span>
        <button type="button" onclick="changeCount('case', 1)">＋</button>
      </div>
    </div>

    <input type="hidden" name="id" value="<?= $item['id'] ?>">
    <input type="hidden" name="name" value="<?= htmlspecialchars($item['name']) ?>">
    <input type="hidden" name="price" value="<?= $item['price'] ?>">
    <input type="hidden" name="volume" value="<?= htmlspecialchars($item['volume']) ?>">
    <input type="hidden" name="count_single" id="form-single" value="0">
    <input type="hidden" name="count_case" id="form-case" value="0">

    <button type="submit" class="cart-btn">カートに入れる</button>
  </form>

  <div class="details">
    <p>商品説明 ▼</p>
    <p style="color:#555;">長州産果実を使った爽やかな地サイダーです。地域限定のレトロラベルが人気。</p>
  </div>
</div>

<script>
function changeCount(type, delta) {
  const countEl = document.getElementById('count-' + type);
  const formEl = document.getElementById('form-' + type);
  let value = parseInt(countEl.textContent);
  value = Math.max(0, value + delta);
  countEl.textContent = value;
  formEl.value = value;
}
</script>

</body>
</html>
