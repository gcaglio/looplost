<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>LoopLost - Benvenuto</title>
  <link rel="stylesheet" href="intro-styles.css" />
  <?php include "ganalytics.php" ?>
</head>
<body>
  <header>
    <div class="header-left">
<!--	    <img src="header_logo.png" alt="LoopLost" class="title"> -->
    </div>
  </header>

  <main>

<div class="welcome-box">
  <div class="welcome-header">
    <h1>Benvenuto in</h1>
    <img src="./images/welcome_logo.png" alt="LoopLost logo" class="welcome-logo" />
  </div>
  <p>Sei pronto a sfidare il tuo ingegno e trovare la via d'uscita dal labirinto?<br>Scegli la difficolt&agrave; e buttati!</p>

<div class="btn-container">
  <button data-size="1">20x20</button>
  <button data-size="2">30x30</button>
  <button data-size="3">40x40</button>
  <button data-size="4">50x50</button>
</div>

</div>


  </main>

	<script>
		document.querySelectorAll('.btn-container button').forEach(button => {
  button.addEventListener('click', () => {
    const size = button.getAttribute('data-size');
    // Carica maze.php con il parametro size
    window.location.href = `maze.php?size=${size}`;
  });
});

	</script>

</body>
</html>

