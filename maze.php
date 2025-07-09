<?php
// Mappa size in folder
$size = $_GET['size'] ?? null;
switch ($size) {
  case '2': $folder = '30x30'; break;
  case '3': $folder = '40x40'; break;
  case '4': $folder = '50x50'; break;
  case '1':
  default:
    $folder = '20x20';
    $size = 1;
    break;
}

// Percorso cartella maze
$mazeDir = __DIR__ . "/mazes/$folder/";

// Prendi tutti i PNG nella cartella
$files = glob($mazeDir . "*.png");
// Pesca un file casuale
if (count($files) > 0) {
  $file = $files[array_rand($files)];
  // Per il src img vogliamo percorso relativo web, supponiamo che la cartella maze sia accessibile da root:
  $fileWeb = "mazes/$folder/" . basename($file);
  
} else {
  // fallback se non ci sono file
  $fileWeb = "immagine.png";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LoopLost</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <!-- Header -->
  <header>
    <div class="header-left">
      <img src="./images/header_logo.png" alt="LoopLost" class="title">
    </div>
    <a href="intro.html" id="homeBtn" title="Torna alla home">🏠 Home</a>
  </header>

  <!-- Toolbar -->
  <div id="toolbar">
    <button id="undo" title="Annulla">↩️</button>
    <button id="clear" title="Cancella">✖️</button>
    <button id="pause" title="Pausa">⏸️</button>
    <button id="stop" title="Stop">⏹️</button>
    <button id="save" title="Salva">💾</button>

    <!-- Bottone rigenera -->
    <form method="get" style="display:inline;">
      <input type="hidden" name="size" value="<?= htmlspecialchars($size) ?>" />
      <button type="submit" title="Rigenera">🔄 Rigenera</button>
    </form>

    <span id="timer">⏱️ 00:00</span>
  </div>

  <!-- Main Drawing Area -->
  <div id="main">
    <div id="container">
      <img id="bg" src="<?= htmlspecialchars($fileWeb) ?>" alt="Sfondo labirinto">
      <canvas id="canvas"></canvas>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 LoopLost. Tutti i diritti riservati.
  </footer>

  <script>
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const cnt = document.getElementById('container');
    const undoBtn = document.getElementById('undo');
    const clearBtn = document.getElementById('clear');
    const pauseBtn = document.getElementById('pause');
    const stopBtn = document.getElementById('stop');
    const saveBtn = document.getElementById('save');
    const timerDisplay = document.getElementById('timer');

    let drawing = false, currentStroke = [];
    let strokes = [];

    let timerStarted = false;
    let timerPaused = false;
    let totalSeconds = 0;
    let timerInterval = null;

    function updateTimer() {
      const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
      const s = String(totalSeconds % 60).padStart(2, '0');
      timerDisplay.textContent = `⏱️ ${m}:${s}`;
    }

    function startTimer() {
      if (timerStarted && !timerPaused) return;
      timerStarted = true;
      timerPaused = false;
      timerInterval = setInterval(() => {
        totalSeconds++;
        updateTimer();
      }, 1000);
    }

    function pauseTimer() {
      if (timerInterval) clearInterval(timerInterval);
      timerPaused = true;
    }

    function stopTimer() {
      if (timerInterval) clearInterval(timerInterval);
      timerStarted = false;
      timerPaused = false;
      totalSeconds = 0;
      updateTimer();
    }

    function resize(){
      canvas.width = cnt.clientWidth;
      canvas.height = cnt.clientHeight;
      redraw();
    }
    window.addEventListener('resize',resize);
    resize();

    function getPos(e){
      const rect = canvas.getBoundingClientRect();
      const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
      const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
      return {x,y};
    }

    function isInBottomRightCorner(p) {
      const thresholdX = canvas.width * 0.98;
      const thresholdY = canvas.height * 0.98;
      return p.x >= thresholdX && p.y >= thresholdY;
    }

    function start(e){
      e.preventDefault();
      const p = getPos(e);
      if (isInBottomRightCorner(p)) {
        pauseTimer();
        return;
      }
      startTimer();
      drawing = true;
      currentStroke = [];
      currentStroke.push(p);
    }

    function move(e){
      if(!drawing) return;
      e.preventDefault();
      const p = getPos(e);
      if (isInBottomRightCorner(p)) {
        pauseTimer();
        drawing = false;
        return;
      }
      currentStroke.push(p);
      redraw();
      drawStroke(currentStroke);
    }

    function end(e){
      if(!drawing) return;
      drawing = false;
      strokes.push(currentStroke);
    }

    function drawStroke(stroke){
      if(stroke.length < 2) return;
      ctx.beginPath();
      ctx.moveTo(stroke[0].x, stroke[0].y);
      for(let i=1; i<stroke.length; i++){
        ctx.lineTo(stroke[i].x, stroke[i].y);
      }
      ctx.strokeStyle = 'red';
      ctx.lineWidth = 3;
      ctx.lineCap = 'round';
      ctx.stroke();
    }

    function redraw(){
      ctx.clearRect(0,0,canvas.width,canvas.height);
      strokes.forEach(s => drawStroke(s));
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    canvas.addEventListener('mouseup', end);
    canvas.addEventListener('mouseout', end);
    canvas.addEventListener('touchstart', start, {passive:false});
    canvas.addEventListener('touchmove', move, {passive:false});
    canvas.addEventListener('touchend', end);

    undoBtn.addEventListener('click', () => {
      strokes.pop();
      redraw();
    });

    clearBtn.addEventListener('click', () => {
      strokes = [];
      redraw();
      pauseTimer();
    });

    pauseBtn.addEventListener('click', () => {
      if(timerPaused) {
        // resume
        timerPaused = false;
        startTimer();
      } else {
        pauseTimer();
      }
    });

    stopBtn.addEventListener('click', () => {
      stopTimer();
    });

    saveBtn.addEventListener('click', () => {
      // salva canvas + strokes dati
      const imageDataUrl = canvas.toDataURL("image/png");

      const data = {
        image: imageDataUrl,
        strokes: strokes
      };

      // Scarica JSON con dati
      const blob = new Blob([JSON.stringify(data)], {type: "application/json"});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = "looplost_disegno.json";
      a.click();
      URL.revokeObjectURL(url);
    });

    updateTimer();
  </script>
</body>
</html>

