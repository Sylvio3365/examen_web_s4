<style>
  body {
    font-family: Arial, sans-serif;
    padding: 20px;
  }

  table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
  }

  th,
  td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
  }

  th {
    background-color: #f9f9f9;
  }

  #message {
    margin-top: 10px;
    padding: 10px;
    border-radius: 4px;
  }

  .success {
    color: green;
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
  }

  .error {
    color: red;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
  }

  .btn {
    padding: 5px 10px;
    margin: 0 2px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
  }

  .btn-success {
    background-color: #28a745;
    color: white;
  }

  .btn-danger {
    background-color: #dc3545;
    color: white;
  }

  .btn:hover {
    opacity: 0.8;
  }

  .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
</style>

<h1>Liste des prêts en attente</h1>

<table>
  <thead>
    <tr>
      <th>ID Prêt</th>
      <th>Client</th>
      <th>Type de prêt</th>
      <th>Montant</th>
      <th>Date statut</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody id="table-body">
    <!-- Les lignes seront ajoutées ici -->
  </tbody>
</table>

<p id="message"></p>

<script>
  const apiUrl = "<?php echo $apiBase ?>/pendingPret";
  const apiBase = "<?php echo $apiBase ?>";

  function chargerPrets() {
    fetch(apiUrl)
      .then(response => {
        if (!response.ok) {
          throw new Error("Erreur réseau");
        }
        return response.json();
      })
      .then(data => {
        const tbody = document.getElementById("table-body");
        tbody.innerHTML = "";

        if (data.length === 0) {
          tbody.innerHTML = "<tr><td colspan='7'>Aucun prêt en attente</td></tr>";
        } else {
          data.forEach(pret => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${pret.idpret}</td>
                <td>${sanitize(pret.nom_client)}</td>
                <td>${sanitize(pret.nom_typepret)}</td>
                <td>${pret.montant || 'N/A'}</td>
                <td>${sanitize(pret.date_modif)}</td>
                <td>${sanitize(pret.statut_valeur)}</td>
                <td>
                  <button class="btn btn-success" onclick="validerPret(${pret.idpret}, ${pret.montant || 0})">
                    Valider
                  </button>
                  <button class="btn btn-danger" onclick="annulerPret(${pret.idpret})">
                    Annuler
                  </button>
                </td>
              `;
            tbody.appendChild(tr);
          });
        }
      })
      .catch(error => {
        afficherMessage("Erreur lors du chargement : " + error.message, "error");
      });
  }

  function validerPret(idpret, montant) {
    if (!montant || montant <= 0) {
      afficherMessage("Montant invalide pour ce prêt", "error");
      return;
    }

    // Désactiver les boutons pendant le traitement
    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.disabled = true);

    const data = {
      idpret: idpret,
      montant: montant
    };

    fetch(apiBase + "/validerPret", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
      })
      .then(response => {
        console.log("Réponse reçue:", response);
        if (!response.ok) {
          throw new Error("Erreur réseau");
        }
        return response.json();
      })
      .then(result => {
        if (result.error) {
          afficherMessage("Erreur : " + result.error, "error");
        } else {
          afficherMessage(result.message || "Prêt validé avec succès", "success");
          // Recharger la liste après un court délai
          setTimeout(() => {
            chargerPrets();
          }, 1000);
        }
      })
      .catch(error => {
        afficherMessage("Erreur lors de la validation : " + error.message, "error");
      })
      .finally(() => {
        // Réactiver les boutons
        buttons.forEach(btn => btn.disabled = false);
      });
  }

  function annulerPret(idpret) {
    if (!confirm("Êtes-vous sûr de vouloir annuler ce prêt ?")) {
      return;
    }

    // Désactiver les boutons pendant le traitement
    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.disabled = true);

    const data = {
      idpret: idpret
    };

    fetch(apiBase + "/annulerPret", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("Erreur réseau");
        }
        return response.json();
      })
      .then(result => {
        if (result.error) {
          afficherMessage("Erreur : " + result.error, "error");
        } else {
          afficherMessage(result.message || "Prêt annulé avec succès", "success");
          // Recharger la liste après un court délai
          setTimeout(() => {
            chargerPrets();
          }, 1000);
        }
      })
      .catch(error => {
        afficherMessage("Erreur lors de l'annulation : " + error.message, "error");
      })
      .finally(() => {
        // Réactiver les boutons
        buttons.forEach(btn => btn.disabled = false);
      });
  }

  function afficherMessage(message, type) {
    const messageEl = document.getElementById("message");
    messageEl.textContent = message;
    messageEl.className = type;

    // Faire disparaître le message après 5 secondes
    setTimeout(() => {
      messageEl.textContent = "";
      messageEl.className = "";
    }, 5000);
  }

  // Protège contre les injections HTML
  function sanitize(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  // Charger les prêts au démarrage
  chargerPrets();
</script>