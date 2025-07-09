# LoopLost 🌀

**LoopLost** is a single-page web application that lets users solve digital mazes directly in the browser. Designed with a mysterious and immersive blue-themed interface, it's built for puzzle lovers, learners, and anyone fascinated by labyrinths.

---

## 🔍 Features

- Randomized solvable mazes in various sizes
- Save and retrieve maze images (PNG format)
- Use PHP for server-side file access
- Organized maze archive by size: 20x20, 30x30, 40x40, 50x50
- Blue-themed modern design

---

## 🛠️ Technologies Used

- **PHP** for backend logic (basically random maze extraction)
- **HTML5 + CSS3** for frontend structure and design
- File-based storage for generated mazes (no database required)
- Python script for offline maze generation
---

## 📁 Project Structure
```
looplost/
  ├── intro.php # Main entry page
  ├── intro-styles.css # style for intro page
  ├── styles.css # style for maze single-page 
  ├── genera_labirinti.py # python script to generate zip archive with random mazes
  ├── maze.php # PHP page to show maze with canvas drawing capabilities
  ├── images/ # App logos and graphics
  └── mazes/ # Stored maze images
        ├── 20x20/
        ├── 30x30/
        ├── 40x40/
        └── 50x50/
```
## 📜 License
This project is licensed under the MIT License. See LICENSE for details.
