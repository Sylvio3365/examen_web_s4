<style>
    #message {
        margin-top: 10px;
    }
</style>

<div class="container">
  <h1 class="mb-4">Liste des prêts en attente</h1>

  <table class="table table-striped" id="liste-prets">
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


  <p id="message" class="alert" role="alert"></p>
</div>

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
      messageEl.className = type === 'success' ? 'alert alert-success' : 'alert alert-danger';
      
      // Faire disparaître le message après 5 secondes
      setTimeout(() => {
        messageEl.textContent = "";
        messageEl.className = 'alert';
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
