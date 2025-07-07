public Form1()
    {
        InitializeComponent();
    }

    private void panel1_Paint(object sender, PaintEventArgs e)
    {
        Graphics g = e.Graphics;

        // Dessine la grille
        for (int i = 0; i <= gridSize; i++)
        {
            g.DrawLine(Pens.Black, i * cellSize, 0, i * cellSize, gridSize * cellSize); // Lignes verticales
            g.DrawLine(Pens.Black, 0, i * cellSize, gridSize * cellSize, i * cellSize); // Lignes horizontales
        }

        // Dessine les points des joueurs
        for (int x = 0; x < gridSize; x++)
        {
            for (int y = 0; y < gridSize; y++)
            {
                if (grid[x, y] == 1)
                {
                    g.FillEllipse(Brushes.Blue, x * cellSize - 10, y * cellSize - 10, 20, 20); // Joueur 1
                }
                else if (grid[x, y] == 2)
                {
                    g.FillEllipse(Brushes.Red, x * cellSize - 10, y * cellSize - 10, 20, 20); // Joueur 2
                }
            }
        }
    }

    private void panel1_MouseClick(object sender, MouseEventArgs e)
    {
        int x = e.X / cellSize; // Calcul de la colonne
        int y = e.Y / cellSize; // Calcul de la ligne

        // Vérifiez si le joueur a atteint la limite de points
        int playerCount = (currentPlayer == 1) ? player1Points.Count : player2Points.Count;

        if (currentPlayer == 1 && playerCount >= pointLimitUn)
        {
            // Supprime le premier point du joueur 1
            if (player1Points.Count > 0)
            {
                Point firstPoint = player1Points.Dequeue();
                grid[firstPoint.X, firstPoint.Y] = 0; // Supprime le point de la grille
            }
        }
        else if (currentPlayer == 2 && playerCount >= pointLimitDeux)
        {
            // Supprime le premier point du joueur 2
            if (player2Points.Count > 0)
            {
                Point firstPoint = player2Points.Dequeue();
                grid[firstPoint.X, firstPoint.Y] = 0; // Supprime le point de la grille
            }
        }

        // Ajoute le nouveau point
        if (x >= 0 && x < gridSize && y >= 0 && y < gridSize && grid[x, y] == 0)
        {
            grid[x, y] = currentPlayer; // Place le point
            if (currentPlayer == 1)
            {
                player1Points.Enqueue(new Point(x, y)); // Ajoute le point à la liste du joueur 1
            }
            else
            {
                player2Points.Enqueue(new Point(x, y)); // Ajoute le point à la liste du joueur 2
            }

            panel1.Invalidate();

            if (CheckForWinner(x, y))
            {
                MessageBox.Show($"Le joueur {currentPlayer} a gagné !");
                ResetGame();
            }
            else
            {
                currentPlayer = (currentPlayer == 1) ? 2 : 1; // Change de joueur
            }

            panel1.Invalidate(); // Redessine le panel
        }
    }

    private bool CheckForWinner(int x, int y)
    {
        return CheckLine(x, y, 1, 0) || // Horizontal
               CheckLine(x, y, 0, 1) || // Vertical
               CheckLine(x, y, 1, 1) || // Diagonale /
               CheckLine(x, y, 1, -1);  // Diagonale \
    }

    private bool CheckLine(int x, int y, int dx, int dy)
    {
        int count = 1;
        count += CountInDirection(x, y, dx, dy); // Vérification dans une direction
        count += CountInDirection(x, y, -dx, -dy); // Vérification dans l'autre direction
        return count >= 5; // Vérifie si 5 alignés
    }

    private int CountInDirection(int x, int y, int dx, int dy)
    {
        int count = 0;
        int player = grid[x, y];

        while (true)
        {
            x += dx;
            y += dy;

            if (x < 0 || x >= gridSize || y < 0 || y >= gridSize || grid[x, y] != player)
            {
                break;
            }

            count++;
        }

        return count;
    }

    private void ResetGame()
    {
        Array.Clear(grid, 0, grid.Length);
        player1Points.Clear();
        player2Points.Clear();
        currentPlayer = 1; // Commence avec le joueur 1
        panel1.Invalidate();
    }

    private void button1_Click(object sender, EventArgs e)
    {
        if (currentPlayer == 2)
        {
            SuggestPoint(2); // Suggérer un point pour le joueur 2
        }
        else
        {
            MessageBox.Show($"Ce n'est pas encore votre tour, c'est au joueur {1} de jouer !");
        }
    }

    private void button2_Click(object sender, EventArgs e)
    {
        if (currentPlayer == 1)
        {
            SuggestPoint(1); // Suggérer un point pour le joueur 1
        }
        else
        {
            MessageBox.Show($"Ce n'est pas encore votre tour, c'est au joueur {2} de jouer !");
        }
    }

    private void SuggestPoint(int joueur)
    {
        // Vérifie d'abord si le joueur a 4 points alignés
        if (CheckForAlignment(joueur, 4))
        {
            SuggestWinningPoint(joueur);
            currentPlayer = (joueur == 1) ? 2 : 1; // Change de joueur
            return;
        }

        // Vérifie si l'adversaire a 4 points alignés
        int adversaire = (joueur == 1) ? 2 : 1;
        if (CheckForAlignment(adversaire, 4))
        {
            SuggestBlockingPoint(adversaire);
            currentPlayer = (joueur == 1) ? 2 : 1; // Change de joueur
            return;
        }

        // Vérifie si le joueur a 3 points alignés
        if (CheckForAlignment(joueur, 3))
        {
            SuggestFourthPoint(joueur);
            currentPlayer = (joueur == 1) ? 2 : 1; // Change de joueur
            return;
        }
    }

    private void SuggestWinningPoint(int joueur)
    {
        for (int x = 0; x < gridSize; x++)
        {
            for (int y = 0; y < gridSize; y++)
            {
                if (grid[x, y] == joueur)
                {
                    int[][] directions = new int[][]
                    {
                        new int[] { 1, 0 },   // Horizontal
                        new int[] { 0, 1 },   // Vertical
                        new int[] { 1, 1 },   // Diagonale /
                        new int[] { 1, -1 }   // Diagonale \
                    };

                    foreach (int[] direction in directions)
                    {
                        int count = 1;
                        count += CountAlignment(x, y, direction[0], direction[1], joueur);
                        count += CountAlignment(x, y, -direction[0], -direction[1], joueur);

                        if (count == 4)
                        {
                            Point? suggestedPosition = FindSuggestedPosition(x, y, direction[0], direction[1], joueur);
                            if (suggestedPosition != null)
                            {
                                grid[suggestedPosition.Value.X, suggestedPosition.Value.Y] = joueur; // Place le point gagnant
                                panel1.Invalidate(); // Redessine le panel
                                if (CheckForWinner(suggestedPosition.Value.X, suggestedPosition.Value.Y))
                                {
                                    MessageBox.Show($"Le joueur {joueur} a gagné !");
                                    ResetGame();
                                }
                                panel1.Invalidate(); // Redessine le panel
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    private void SuggestBlockingPoint(int adversaire)
    {
        for (int x = 0; x < gridSize; x++)
        {
            for (int y = 0; y < gridSize; y++)
            {
                if (grid[x, y] == adversaire)
                {
                    int[][] directions = new int[][]
                    {
                        new int[] { 1, 0 },   // Horizontal
                        new int[] { 0, 1 },   // Vertical
                        new int[] { 1, 1 },   // Diagonale /
                        new int[] { 1, -1 }   // Diagonale \
                    };

                    foreach (int[] direction in directions)
                    {
                        int count = 1;
                        count += CountAlignment(x, y, direction[0], direction[1], adversaire);
                        count += CountAlignment(x, y, -direction[0], -direction[1], adversaire);

                        if (count == 4)
                        {
                            Point? blockingPosition = FindSuggestedPosition(x, y, direction[0], direction[1], adversaire);
                            if (blockingPosition != null)
                            {
                                grid[blockingPosition.Value.X, blockingPosition.Value.Y] = (adversaire == 1) ? 2 : 1; // Place le point pour bloquer
                                panel1.Invalidate(); // Redessine le panel
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    private void SuggestFourthPoint(int joueur)
    {
        for (int x = 0; x < gridSize; x++)
        {
            for (int y = 0; y < gridSize; y++)
            {
                if (grid[x, y] == joueur)
                {
                    int[][] directions = new int[][]
                    {
                        new int[] { 1, 0 },   // Horizontal
                        new int[] { 0, 1 },   // Vertical
                        new int[] { 1, 1 },   // Diagonale /
                        new int[] { 1, -1 }   // Diagonale \
                    };

                    foreach (int[] direction in directions)
                    {
                        int count = 1;
                        count += CountAlignment(x, y, direction[0], direction[1], joueur);
                        count += CountAlignment(x, y, -direction[0], -direction[1], joueur);

                        if (count == 3)
                        {
                            Point? suggestedPosition = FindSuggestedPosition(x, y, direction[0], direction[1], joueur);
                            if (suggestedPosition != null)
                            {
                                grid[suggestedPosition.Value.X, suggestedPosition.Value.Y] = joueur; // Place le point suggéré
                                panel1.Invalidate(); // Redessine le panel
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    private bool CheckForAlignment(int joueur, int neededCount)
    {
        for (int x = 0; x < gridSize; x++)
        {
            for (int y = 0; y < gridSize; y++)
            {
                if (grid[x, y] == joueur)
                {
                    int[][] directions = new int[][]
                    {
                        new int[] { 1, 0 },   // Horizontal
                        new int[] { 0, 1 },   // Vertical
                        new int[] { 1, 1 },   // Diagonale /
                        new int[] { 1, -1 }   // Diagonale \
                    };

                    foreach (int[] direction in directions)
                    {
                        int count = 1;
                        count += CountAlignment(x, y, direction[0], direction[1], joueur);
                        count += CountAlignment(x, y, -direction[0], -direction[1], joueur);

                        if (count >= neededCount && HasEmptySpaceForAlignment(x, y, direction[0], direction[1], joueur))
                        {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private int CountAlignment(int x, int y, int dx, int dy, int joueur)
    {
        int count = 0;
        while (true)
        {
            x += dx;
            y += dy;

            if (x < 0 || x >= gridSize || y < 0 || y >= gridSize || grid[x, y] != joueur)
            {
                break;
            }

            count++;
        }

        return count;
    }

    private Point? FindSuggestedPosition(int x, int y, int dx, int dy, int joueur)
    {
        int xBefore = x + dx;
        int yBefore = y + dy;
        int xAfter = x - dx;
        int yAfter = y - dy;

        if (IsInBounds(xBefore, yBefore) && grid[xBefore, yBefore] == 0)
        {
            return new Point(xBefore, yBefore);
        }
        if (IsInBounds(xAfter, yAfter) && grid[xAfter, yAfter] == 0)
        {
            return new Point(xAfter, yAfter);
        }

        return null;
    }

    private bool IsInBounds(int x, int y)
    {
        return x >= 0 && x < gridSize && y >= 0 && y < gridSize;
    }

    private bool HasEmptySpaceForAlignment(int x, int y, int dx, int dy, int joueur)
    {
        int xBefore = x + dx;
        int yBefore = y + dy;
        int xAfter = x - dx;
        int yAfter = y - dy;

        return (IsInBounds(xBefore, yBefore) && grid[xBefore, yBefore] == 0) ||
               (IsInBounds(xAfter, yAfter) && grid[xAfter, yAfter] == 0);
    }

    private void SaveGame()
    {
        using (StreamWriter writer = new StreamWriter("saved_game.txt"))
        {
            writer.WriteLine(currentPlayer);
            for (int x = 0; x < gridSize; x++)
            {
                for (int y = 0; y < gridSize; y++)
                {
                    writer.Write(grid[x, y] + (y < gridSize - 1 ? "," : ""));
                }
                writer.WriteLine();
            }
        }
        MessageBox.Show("Partie sauvegardée !");
    }

    private void LoadGame()
    {
        if (!File.Exists("saved_game.txt"))
        {
            MessageBox.Show("Aucune partie sauvegardée !");
            return;
        }

        using (StreamReader reader = new StreamReader("saved_game.txt"))
        {
            currentPlayer = int.Parse(reader.ReadLine());
            for (int x = 0; x < gridSize; x++)
            {
                string line = reader.ReadLine();
                string[] values = line.Split(',');
                for (int y = 0; y < gridSize; y++)
                {
                    grid[x, y] = int.Parse(values[y]);
                    if (grid[x, y] == 1)
                    {
                        player1Points.Enqueue(new Point(x, y)); // Ajoute le point à la queue du joueur 1
                    }
                    else if (grid[x, y] == 2)
                    {
                        player2Points.Enqueue(new Point(x, y)); // Ajoute le point à la queue du joueur 2
                    }
                }
            }
        }
        panel1.Invalidate();
        MessageBox.Show("Partie chargée !");
    }


    private void button4_Click(object sender, EventArgs e)
    {
        // Valider le contenu de textBox2
        if (int.TryParse(textBox2.Text, out int limit))
        {
            pointLimitUn = limit; // Met à jour la limite de points
            MessageBox.Show($"La limite de points pour le joueur 1 est maintenant {limit}.");
        }
        else
        {
            MessageBox.Show("Veuillez entrer un nombre valide pour la limite de points.");
        }
    }

    private void button3_Click(object sender, EventArgs e)
    {
        // Valider le contenu de textBox1
        if (int.TryParse(textBox1.Text, out int limit))
        {
            pointLimitDeux = limit; // Met à jour la limite de points
            MessageBox.Show($"La limite de points pour le joueur 2 est maintenant {limit}.");
        }
        else
        {
            MessageBox.Show("Veuillez entrer un nombre valide pour la limite de points.");
        }
    }
    private void textBox1_TextChanged(object sender, EventArgs e)
    {
    }

    private void textBox2_TextChanged(object sender, EventArgs e)
    {
    }
}