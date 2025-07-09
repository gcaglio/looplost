
import uuid
import os
from PIL import Image
import random
import zipfile

def generate_maze_dfs(width, height):
    maze = [["#" for _ in range(width)] for _ in range(height)]
    directions = [(0, -2), (2, 0), (0, 2), (-2, 0)]

    def is_valid(nx, ny):
        return 0 < nx < width - 1 and 0 < ny < height - 1

    def carve(x, y):
        maze[y][x] = " "
        random.shuffle(directions)
        for dx, dy in directions:
            nx, ny = x + dx, y + dy
            if is_valid(nx, ny) and maze[ny][nx] == "#":
                maze[y + dy // 2][x + dx // 2] = " "
                carve(nx, ny)

    carve(1, 1)
    maze[0][1] = " "
    maze[height - 1][width - 2] = " "
    return maze

def generate_maze_prim(width, height):
    maze = [["#" for _ in range(width)] for _ in range(height)]
    walls = [(1, 1)]
    maze[1][1] = " "
    directions = [(0, -2), (2, 0), (0, 2), (-2, 0)]

    while walls:
        x, y = walls.pop(random.randint(0, len(walls) - 1))
        if maze[y][x] == "#":
            neighbors = []
            for dx, dy in directions:
                nx, ny = x + dx, y + dy
                if 0 < nx < width - 1 and 0 < ny < height - 1:
                    if maze[ny][nx] == " ":
                        neighbors.append((nx, ny))
            if len(neighbors) == 1:
                maze[y][x] = " "
                nx, ny = neighbors[0]
                maze[(y + ny) // 2][(x + nx) // 2] = " "
                for dx, dy in directions:
                    wx, wy = x + dx, y + dy
                    if 0 < wx < width - 1 and 0 < wy < height - 1 and maze[wy][wx] == "#":
                        walls.append((wx, wy))

    maze[0][1] = " "
    maze[height - 1][width - 2] = " "
    return maze

def generate_maze_kruskal(width, height):
    parent = {}
    def find(cell):
        while parent[cell] != cell:
            parent[cell] = parent[parent[cell]]
            cell = parent[cell]
        return cell

    def union(a, b):
        root_a = find(a)
        root_b = find(b)
        if root_a != root_b:
            parent[root_b] = root_a
            return True
        return False

    maze = [["#" for _ in range(width)] for _ in range(height)]
    cells = [(x, y) for y in range(1, height, 2) for x in range(1, width, 2)]
    for cell in cells:
        parent[cell] = cell
        maze[cell[1]][cell[0]] = " "

    walls = []
    for x, y in cells:
        if x < width - 2:
            walls.append(((x, y), (x + 2, y), (x + 1, y)))
        if y < height - 2:
            walls.append(((x, y), (x, y + 2), (x, y + 1)))
    random.shuffle(walls)

    for cell1, cell2, wall in walls:
        if union(cell1, cell2):
            maze[wall[1]][wall[0]] = " "

    maze[0][1] = " "
    maze[height - 1][width - 2] = " "
    return maze

def draw_maze_png(maze, filename, cell_size=10):
    maze_array = [[1 if cell == "#" else 0 for cell in row] for row in maze]
    h = len(maze_array)
    w = len(maze_array[0])

    img = Image.new("RGB", (w * cell_size, h * cell_size), "white")
    pixels = img.load()

    for y in range(h):
        for x in range(w):
            color = (0, 0, 0) if maze_array[y][x] == 1 else (255, 255, 255)
            for i in range(cell_size):
                for j in range(cell_size):
                    pixels[x * cell_size + i, y * cell_size + j] = color

    img.save(filename)

def main():
    configurations = [
        (20, 20, 100),
        (30, 30, 100),
        (40, 40, 100),
        (50, 50, 100),
        (60, 50, 200),
    ]

    algorithms = [
        ("dfs", generate_maze_dfs),
        ("prim", generate_maze_prim),
        ("kruskal", generate_maze_kruskal),
    ]

    output_dir = "mazes_output"
    os.makedirs(output_dir, exist_ok=True)

    for width_cells, height_cells, count in configurations:
        width = width_cells * 2 + 1
        height = height_cells * 2 + 1
        for _ in range(count):
            algo_name, algo_func = random.choice(algorithms)
            maze = algo_func(width, height)
            uid = str(uuid.uuid4())
            filename = f"{width_cells}_{height_cells}_media_{algo_name}_{uid}.png"
            full_path = os.path.join(output_dir, filename)
            draw_maze_png(maze, full_path)

    zip_path = "labirinti_media_png.zip"
    with zipfile.ZipFile(zip_path, 'w') as zipf:
        for root, _, files in os.walk(output_dir):
            for file in files:
                zipf.write(os.path.join(root, file), arcname=file)

    print(f"Labirinti generati e salvati in {zip_path}")

if __name__ == "__main__":
    main()
